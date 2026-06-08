#!/bin/bash
set -e

# Create required directories if they don't exist
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/bootstrap/cache

# Set proper permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

cp -n .env.example /var/www/html/.env

composer install

# Generate application key if it doesn't exist
if [ -z "$(grep '^APP_KEY=' .env | grep -v '=$')" ]; then
    php artisan key:generate
fi

php artisan migrate

php artisan rabbitmq:declare-all

# Execute the provided command (which is typically php-fpm)
exec "$@"
