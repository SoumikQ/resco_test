#!/bin/sh
set -e

echo "🚀 Initializing RESCO Restaurant Management System..."

# 1. Ensure storage and bootstrap directories exist with write permissions
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/storage/app/public \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 2. Check and copy environment file if not exists
if [ ! -f /var/www/html/.env ]; then
    echo "📄 .env file not found, initializing environment..."
    if [ -f /var/www/html/.env.docker.example ]; then
        cp /var/www/html/.env.docker.example /var/www/html/.env
    elif [ -f /var/www/html/.env.example ]; then
        cp /var/www/html/.env.example /var/www/html/.env
    fi
fi

# 3. Configure Nginx port dynamically (Essential for Render.com $PORT)
export PORT=${PORT:-80}
echo "🌐 Configuring Web Server on Port: $PORT..."
if [ -f /etc/nginx/nginx.conf.template ]; then
    envsubst '\$PORT' < /etc/nginx/nginx.conf.template > /etc/nginx/conf.d/default.conf
    # Also update sites-available/default if debian nginx is used
    mkdir -p /etc/nginx/sites-available /etc/nginx/sites-enabled
    envsubst '\$PORT' < /etc/nginx/nginx.conf.template > /etc/nginx/sites-available/default
    ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default
fi

# 4. Generate Application Key if empty
if [ -z "$APP_KEY" ]; then
    APP_KEY=$(grep -E "^APP_KEY=" /var/www/html/.env | cut -d '=' -f2)
fi
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating Laravel Application Key..."
    php artisan key:generate --force
fi

# 5. Create storage symbolic link
if [ ! -L /var/www/html/public/storage ]; then
    echo "🔗 Creating storage symlink..."
    php artisan storage:link || true
fi

# 6. Database Connection & Migration (Non-blocking retry)
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

# 7. Production Caching
if [ "$APP_ENV" = "production" ]; then
    echo "⚡ Optimizing Laravel for Production..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
else
    php artisan optimize:clear || true
fi

echo "🎉 RESCO Application initialized successfully!"

exec "$@"
