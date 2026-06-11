#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."

REMOTE="${DEPLOY_BRANCH:-main}"
LOCAL="$(git rev-parse @)"
REMOTE_HASH="$(git ls-remote origin "refs/heads/$REMOTE" | awk '{print $1}')"

if [ -z "$REMOTE_HASH" ]; then
    echo "Could not resolve remote branch origin/$REMOTE"
    exit 1
fi

if [ "$LOCAL" = "$REMOTE_HASH" ]; then
    echo "Already up to date ($LOCAL)"
    exit 0
fi

echo "New commits detected. Updating from origin/$REMOTE..."
git pull origin "$REMOTE"
./deploy/post-deploy.sh
