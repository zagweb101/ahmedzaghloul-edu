#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$SCRIPT_DIR/.."
# shellcheck source=deploy/common.sh
source "$SCRIPT_DIR/common.sh"

echo "==> Using PHP: $PHP_BIN ($($PHP_BIN -r 'echo PHP_VERSION;'))"

echo "==> Installing production dependencies"
if [ -f composer.phar ]; then
    $PHP_BIN composer.phar install --no-dev --optimize-autoloader --no-interaction
elif command -v composer2 >/dev/null 2>&1; then
    $PHP_BIN "$(command -v composer2)" install --no-dev --optimize-autoloader --no-interaction
elif command -v composer >/dev/null 2>&1; then
    $PHP_BIN "$(command -v composer)" install --no-dev --optimize-autoloader --no-interaction
else
    echo "Composer not found"
    exit 1
fi

echo "==> Running migrations"
$PHP_BIN artisan migrate --force

echo "==> Linking public storage"
$PHP_BIN artisan storage:link --force 2>/dev/null || $PHP_BIN artisan storage:link

echo "==> Caching configuration"
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache

echo "==> Production health check"
$PHP_BIN artisan platform:health-check

echo "==> Done. Verify: $PHP_BIN artisan schedule:list"
