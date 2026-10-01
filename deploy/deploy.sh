#!/usr/bin/env bash
set -Eeuo pipefail

APP_DIR="${APP_DIR:-/var/www/uytop}"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"

cd "$APP_DIR"
test -f .env || { echo "Missing $APP_DIR/.env"; exit 1; }

$PHP_BIN artisan down --retry=60 || true
trap '$PHP_BIN artisan up' EXIT

git pull --ff-only origin main
$COMPOSER_BIN install --no-dev --prefer-dist --no-interaction --optimize-autoloader
npm ci
npm run build
$PHP_BIN artisan migrate --force
$PHP_BIN artisan storage:link || true
$PHP_BIN artisan optimize:clear
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache
$PHP_BIN artisan queue:restart

if [[ "${SEED_DEMO:-0}" == "1" ]]; then
    $PHP_BIN artisan db:seed --force
fi

$PHP_BIN artisan up
trap - EXIT
echo "UyTop deployment completed. Check ${APP_URL:-https://your-domain.example}/up"
