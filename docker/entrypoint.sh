#!/bin/sh
set -e

# Install PHP dependencies only if vendor is not mounted from host
if [ ! -f "vendor/autoload.php" ]; then
    echo "vendor/autoload.php not found - attempting composer install..."
    composer install --no-interaction || echo "WARNING: composer install failed. Mount vendor/ from server if using private packages."
elif [ -f "vendor/autoload.php" ]; then
    echo "Using mounted vendor directory."
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
