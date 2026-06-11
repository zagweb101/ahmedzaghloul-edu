#!/usr/bin/env bash
set -euo pipefail

echo "==> Installing production dependencies"
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Running migrations"
php artisan migrate --force

echo "==> Linking public storage"
php artisan storage:link --force 2>/dev/null || php artisan storage:link

echo "==> Caching configuration"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Done. Verify: php artisan schedule:list"
