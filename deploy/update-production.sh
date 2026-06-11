#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$SCRIPT_DIR/.."
# shellcheck source=deploy/common.sh
source "$SCRIPT_DIR/common.sh"

echo "==> Pulling latest changes"
git pull origin main

./deploy/post-deploy.sh

echo "==> Production update complete"
