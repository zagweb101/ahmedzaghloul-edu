#!/usr/bin/env bash
set -euo pipefail

REPO_URL="${REPO_URL:-https://github.com/zagweb101/ahmedzaghloul-edu.git}"
PROJECT_DIR="${1:-$HOME/ahmedzaghloul-edu}"

if [ -d "$PROJECT_DIR/.git" ]; then
    echo "المجلد موجود بالفعل: $PROJECT_DIR"
    exit 1
fi

echo "==> Cloning repository"
git clone "$REPO_URL" "$PROJECT_DIR"
cd "$PROJECT_DIR"

if [ ! -f .env ]; then
    echo "==> Creating .env from example"
    cp .env.example .env
    php artisan key:generate
    echo ""
    echo "عدّل ملف .env قبل المتابعة (قاعدة البيانات، APP_URL، البريد، الدفع)."
    echo "ثم شغّل: ./deploy/post-deploy.sh"
    exit 0
fi

./deploy/post-deploy.sh
