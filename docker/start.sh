#!/bin/bash
set -e

# Set port from Render or default to 80
PORT=${PORT:-80}
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/000-default.conf
sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/laravel.conf

# Set permissions (include public for static assets)
chown -R www-data:www-data /var/www/html/public /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 755 /var/www/html/public

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    export APP_KEY=$(php /var/www/html/artisan key:generate --show)
fi

# Run package discover (skipped during build with --no-scripts)
cd /var/www/html && php artisan package:discover --no-interaction

# Wait for PostgreSQL to be ready (Render internal)
if [ "$DB_CONNECTION" = "pgsql" ]; then
    echo "Waiting for PostgreSQL..."
    for attempt in $(seq 1 60); do
        if pg_isready -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME" -d "$DB_DATABASE" > /dev/null 2>&1; then
            echo "PostgreSQL is ready!"
            break
        fi

        if [ "$attempt" -eq 60 ]; then
            echo "PostgreSQL is unavailable after 60 seconds."
            exit 1
        fi

        sleep 1
    done
fi

# Run migrations
cd /var/www/html && php artisan migrate --force --no-interaction

# Only seed admin user (not demo data) — AdminSeeder skips password reset if admin already exists
cd /var/www/html && php artisan db:seed --class=AdminSeeder --force --no-interaction

# Storage link
cd /var/www/html && php artisan storage:link --no-interaction || true

# Cache config for production
cd /var/www/html && php artisan optimize:clear --no-interaction
cd /var/www/html && php artisan optimize --no-interaction

# Start Apache and the queue worker
exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
