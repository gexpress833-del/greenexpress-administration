#!/bin/bash
set -e

# Set port from Render or default to 80
PORT=${PORT:-80}
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/000-default.conf
sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/laravel.conf

# Create SQLite database if it does not exist
if [ ! -f /var/www/html/database/database.sqlite ]; then
    touch /var/www/html/database/database.sqlite
    chown www-data:www-data /var/www/html/database/database.sqlite
fi

# Set permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    export APP_KEY=$(php /var/www/html/artisan key:generate --show)
fi

# Run migrations
cd /var/www/html && php artisan migrate --force --no-interaction

# Storage link
cd /var/www/html && php artisan storage:link --no-interaction || true

# Cache config for production
cd /var/www/html && php artisan config:cache --no-interaction || true
cd /var/www/html && php artisan route:cache --no-interaction || true
cd /var/www/html && php artisan view:cache --no-interaction || true

# Start Apache
apache2-foreground
