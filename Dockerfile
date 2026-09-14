# ==============================================================================
# RESCO Restaurant Management System - Production Ready Dockerfile
# Optimized for: Render.com, Cloud VPS, and Local Docker Compose
# ==============================================================================

FROM php:8.2-fpm

# 1. Set environment variables
ENV DEBIAN_FRONTEND=noninteractive
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PORT=80

# 2. Install system packages, Nginx, Supervisor, and dependencies for PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    supervisor \
    gettext-base \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libicu-dev \
    default-mysql-client \
    ca-certificates \
    gnupg \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 3. Configure and install required PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        intl \
        zip \
        opcache

# 4. Install Composer (Official Image)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 5. Install Node.js 20.x (LTS) & NPM for Vite asset compilation
RUN mkdir -p /etc/apt/keyrings \
    && curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_20.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list \
    && apt-get update \
    && apt-get install -y --no-install-recommends nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 6. Set working directory
WORKDIR /var/www/html

# 7. Copy composer and package manifests first for efficient Docker layer caching
COPY composer.json composer.lock* package.json package-lock.json* ./

# 8. Install PHP and Node dependencies
RUN composer install --no-scripts --no-autoloader --prefer-dist --no-progress --no-interaction || true \
    && npm install --no-audit --no-fund || true

# 9. Copy entire application codebase
COPY . .

# 10. Generate optimized Composer classmap autoload & build frontend Vite assets
RUN composer dump-autoload --optimize \
    && npm run build || true

# 11. Copy custom PHP, Nginx, and Supervisor configurations
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini
COPY docker/nginx/nginx.conf.template /etc/nginx/nginx.conf.template
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# 12. Setup entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

# 13. Set correct ownership and permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 14. Expose HTTP Port
EXPOSE 80 10000

# 15. Set entrypoint and default command (Runs Supervisor to manage both Nginx & PHP-FPM)
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
