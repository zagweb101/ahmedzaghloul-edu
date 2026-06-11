#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$SCRIPT_DIR/.."
# shellcheck source=deploy/common.sh
source "$SCRIPT_DIR/common.sh"

echo "==> Seeding production content (blog, SEO, live stream metadata)"
$PHP_BIN artisan db:seed --class=ProductionContentSeeder --force

echo "==> Done. Review /blog and /admin/blog-posts"
