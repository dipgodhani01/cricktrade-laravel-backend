FROM php:8.2-fpm

WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip nginx supervisor libzip-dev && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application
COPY . .

# Basic nginx config
RUN echo 'server { listen 80; root /var/www/public; index index.php; \
    location / { try_files \$uri \$uri/ /index.php?\$query_string; } \
    location ~ \.php\$ { fastcgi_pass 127.0.0.1:9000; include fastcgi_params; \
    fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name; } }' \
    > /etc/nginx/sites-available/default

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

EXPOSE 80

# Simple start command
CMD php artisan key:generate --force && \
    ./supervisord -c /etc/supervisor/conf.d/supervisord.conf