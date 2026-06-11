#!/usr/bin/env bash
# Enable test/demo payments before commercial launch.
# Usage: bash deploy/enable-demo-payments.sh [demo|stripe|manual]
#   demo   — instant fake purchases (default)
#   stripe — Stripe Checkout test mode (needs sk_test_* in .env)
#   manual — show demo bank details + manual approval flow
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$SCRIPT_DIR/.."
# shellcheck source=deploy/common.sh
source "$SCRIPT_DIR/common.sh"

MODE="${1:-demo}"

if [ ! -f .env ]; then
    echo "ERROR: .env not found in $(pwd)"
    exit 1
fi

set_env() {
    local key="$1"
    local value="$2"

    if grep -q "^${key}=" .env; then
        sed -i "s|^${key}=.*|${key}=${value}|" .env
    else
        echo "${key}=${value}" >> .env
    fi
}

echo "==> Enabling payment demo mode (${MODE})"

set_env PAYMENT_DEMO_MODE true
set_env PAYMENT_DEMO_BANK_NAME '"بنك تجريبي — لا تحوّل مبلغًا حقيقيًا"'
set_env PAYMENT_DEMO_ACCOUNT_NAME '"أكاديمية أحمد زغلول (حساب تجريبي)"'
set_env PAYMENT_DEMO_IBAN '"SA00 8000 0000 6080 0000 0001"'
set_env PAYMENT_DEMO_INSTRUCTIONS '"وضع تجريبي: بيانات وهمية للعرض فقط. للتجربة السريعة استخدم تفعيل الاشتراك الآن أو بطاقة Stripe 4242."'

case "$MODE" in
    demo)
        set_env PAYMENT_DRIVER demo
        ;;
    stripe)
        set_env PAYMENT_DRIVER stripe
        if ! grep -q '^STRIPE_SECRET_KEY=sk_test_' .env; then
            set_env STRIPE_SECRET_KEY sk_test_REPLACE_WITH_YOUR_STRIPE_TEST_KEY
            echo "WARN: Set STRIPE_SECRET_KEY=sk_test_... in .env from Stripe Dashboard"
        fi
        ;;
    manual)
        set_env PAYMENT_DRIVER manual
        ;;
    *)
        echo "Unknown mode: $MODE (use demo, stripe, or manual)"
        exit 1
        ;;
esac

$PHP_BIN artisan config:clear
$PHP_BIN artisan cache:clear

echo "==> Demo payments enabled"
echo "    PAYMENT_DEMO_MODE=true"
echo "    PAYMENT_DRIVER=$(grep '^PAYMENT_DRIVER=' .env | cut -d= -f2-)"
echo ""
echo "To revert before launch: bash deploy/disable-demo-payments.sh"
