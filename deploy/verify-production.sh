#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$SCRIPT_DIR/.."
# shellcheck source=deploy/common.sh
source "$SCRIPT_DIR/common.sh"

echo "==> PHP binary: $PHP_BIN"
echo "==> PHP version: $($PHP_BIN -r 'echo PHP_VERSION;')"

echo ""
echo "==> Environment"
$PHP_BIN artisan about --only=environment 2>/dev/null || $PHP_BIN artisan env

echo ""
echo "==> Platform health check"
$PHP_BIN artisan platform:health-check

echo ""
echo "==> Storage link"
if [ -L public/storage ]; then
    echo "OK: public/storage مرتبط"
else
    echo "WARN: شغّل php artisan storage:link"
fi

echo ""
echo "==> Writable directories"
for dir in storage bootstrap/cache; do
    if [ -w "$dir" ]; then
        echo "OK: $dir قابل للكتابة"
    else
        echo "FAIL: $dir غير قابل للكتابة"
    fi
done

echo ""
echo "==> Scheduled tasks"
$PHP_BIN artisan schedule:list

echo ""
echo "==> Recent log errors (7 days)"
$PHP_BIN artisan platform:log-review

echo ""
echo "==> Database connection"
$PHP_BIN artisan db:show 2>/dev/null || $PHP_BIN artisan migrate:status
