#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."

echo "==> Environment"
php artisan about --only=environment 2>/dev/null || php artisan env

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
php artisan schedule:list

echo ""
echo "==> Database connection"
php artisan db:show 2>/dev/null || php artisan migrate:status
