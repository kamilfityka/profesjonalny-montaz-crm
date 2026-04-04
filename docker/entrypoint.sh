#!/bin/bash
set -e

# Install PHP dependencies if vendor directory is missing
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction
fi

# Generate app key if not set
if [ -z "$APP_KEY" ] && [ -f ".env" ]; then
    php artisan key:generate --ansi --no-interaction 2>/dev/null || true
fi

# Run migrations if database is available
php artisan migrate --force --no-interaction 2>/dev/null || true

# Cache config
php artisan config:cache --no-interaction 2>/dev/null || true
php artisan route:cache --no-interaction 2>/dev/null || true

exec "$@"
