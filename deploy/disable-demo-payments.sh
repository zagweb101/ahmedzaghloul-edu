#!/usr/bin/env bash
# Disable demo payments and restore manual bank transfer mode.
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$SCRIPT_DIR/.."
# shellcheck source=deploy/common.sh
source "$SCRIPT_DIR/common.sh"

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

echo "==> Disabling payment demo mode"

set_env PAYMENT_DEMO_MODE false
set_env PAYMENT_DRIVER manual

$PHP_BIN artisan config:clear
$PHP_BIN artisan cache:clear

echo "==> Demo mode disabled. Set real PAYMENT_IBAN and PAYMENT_BANK_NAME in .env before launch."
