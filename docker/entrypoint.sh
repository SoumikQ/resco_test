#!/bin/sh
set -e

echo "🚀 Initializing RESCO Restaurant Management System..."

# 1. Ensure storage and bootstrap directories exist with full read/write/execute permissions (777)
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/storage/app/public \
         /var/www/html/bootstrap/cache

touch /var/www/html/storage/logs/laravel.log
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 2. Check and copy environment file if not exists
if [ ! -f /var/www/html/.env ]; then
    echo "📄 .env file not found, initializing environment..."
    if [ -f /var/www/html/.env.docker.example ]; then
        cp /var/www/html/.env.docker.example /var/www/html/.env
    elif [ -f /var/www/html/.env.example ]; then
        cp /var/www/html/.env.example /var/www/html/.env
    else
        touch /var/www/html/.env
    fi
    chmod 666 /var/www/html/.env
fi

# 3. Configure Nginx port dynamically (Essential for Render.com $PORT)
export PORT=${PORT:-80}
echo "🌐 Configuring Web Server on Port: $PORT..."
if [ -f /etc/nginx/nginx.conf.template ]; then
    envsubst '\$PORT' < /etc/nginx/nginx.conf.template > /etc/nginx/conf.d/default.conf
    mkdir -p /etc/nginx/sites-available /etc/nginx/sites-enabled
    envsubst '\$PORT' < /etc/nginx/nginx.conf.template > /etc/nginx/sites-available/default
    ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default
fi

# 4. Handle and guarantee Application Encryption Key
if [ -z "$APP_KEY" ]; then
    APP_KEY=$(grep -E "^APP_KEY=" /var/www/html/.env 2>/dev/null | cut -d '=' -f2- | tr -d ' "\r\n')
fi

if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating new Laravel Application Key..."
    NEW_KEY=$(php artisan key:generate --show --no-interaction)
    export APP_KEY="$NEW_KEY"
    if grep -q "^APP_KEY=" /var/www/html/.env 2>/dev/null; then
        sed -i "s|^APP_KEY=.*|APP_KEY=${NEW_KEY}|" /var/www/html/.env
    else
        echo "APP_KEY=${NEW_KEY}" >> /var/www/html/.env
    fi
    echo "✅ Generated APP_KEY: $NEW_KEY"
fi

# 5. Create storage symbolic link
if [ ! -L /var/www/html/public/storage ]; then
    echo "🔗 Creating storage symlink..."
    php artisan storage:link || true
fi

# 6. Database Connection & Migration (Non-blocking check)
if [ -n "$DB_HOST" ] && [ "$DB_HOST" != "127.0.0.1" ] && [ "$DB_HOST" != "localhost" ]; then
    echo "⏳ Checking database connection at ${DB_HOST}:${DB_PORT:-3306}..."
    max_tries=15
    count=0
    until php -r "
    try {
        \$dbh = new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: '3306'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
        exit(0);
    } catch (Exception \$e) {
        exit(1);
    }
    " 2>/dev/null; do
        count=$((count + 1))
        if [ $count -gt $max_tries ]; then
            echo "⚠️ Database connection not ready yet. Skipping blocking wait..."
            break
        fi
        echo "   ... connecting to database ($count/$max_tries)"
        sleep 2
    done

    echo "🗄️ Running database migrations..."
    php artisan migrate --force || echo "Migrations completed or skipped."
fi

# 7. Production Caching & Permissions Check
if [ "$APP_ENV" = "production" ]; then
    echo "⚡ Optimizing Laravel for Production..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
else
    php artisan optimize:clear || true
fi

# Final permission check before launching supervisor
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

echo "🎉 RESCO Application initialized successfully!"

exec "$@"
