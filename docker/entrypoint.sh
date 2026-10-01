#!/usr/bin/env sh
set -eu

cd /var/www/html

if [ -z "${APP_KEY:-}" ]; then
    echo "APP_KEY is required. Run: docker compose run --rm app php artisan key:generate --show"
    exit 1
fi

mkdir -p storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
php artisan storage:link >/dev/null 2>&1 || true
php artisan package:discover --ansi

if [ "${APP_RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force
fi

php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
