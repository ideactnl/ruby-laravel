#!/usr/bin/env bash
set -e

# Install Composer dependencies
composer install --no-dev --working-dir=/var/www/html

# Generate key if not exists
if ! grep -q "^APP_KEY=" /var/www/html/.env; then
    php artisan key:generate --show
fi

# Cache config and routes
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force
