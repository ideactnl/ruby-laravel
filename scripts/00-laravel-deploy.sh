#!/usr/bin/env bash
set -e

# Install Composer dependencies
composer install --no-dev --working-dir=/var/www/html

# Clear config cache to ensure fresh env
php artisan config:clear

# Generate Scribe API docs with correct config
php artisan scribe:generate --no-interaction

# Cache config and routes
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Log completion
echo "Deployment script completed successfully."
