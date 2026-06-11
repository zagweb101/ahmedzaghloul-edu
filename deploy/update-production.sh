#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."

echo "==> Pulling latest changes"
git pull origin main

./deploy/post-deploy.sh

echo "==> Production update complete"
