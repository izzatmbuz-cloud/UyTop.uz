# UyTop production deployment

## Server requirements

- Ubuntu 22.04/24.04, Nginx, MySQL 8
- PHP 8.2+ with `fpm`, `mysql`, `mbstring`, `xml`, `curl`, `zip`, `gd`, `bcmath`
- Composer 2, Node.js 20+, npm, Git, Supervisor, Certbot

## First deployment

1. Clone the repository to `/var/www/uytop` and set the web root to `/var/www/uytop/public`.
2. Copy `.env.production.example` to `.env`, replace every `CHANGE_ME`, set the real domain and run `php artisan key:generate`.
3. Create the MySQL database and user, then run `composer install --no-dev --optimize-autoloader` and `npm ci && npm run build`.
4. Run `php artisan migrate --force`, `php artisan storage:link`, and optionally `php artisan db:seed --force` for the protected demo accounts/data.
5. Give `www-data` write access only to `storage` and `bootstrap/cache`.
6. Install `deploy/nginx.conf.example`, adjust the domain/PHP socket, test Nginx, then obtain HTTPS with Certbot.
7. Install `deploy/uytop-worker.conf.example` in Supervisor and start it.
8. Add `* * * * * cd /var/www/uytop && php artisan schedule:run >> /dev/null 2>&1` to the deploy user's crontab.
9. Run `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`.
10. Verify `/up`, registration/email verification, login, image upload, AI parsing, moderation, request completion, and commission status.

For subsequent releases run `APP_DIR=/var/www/uytop bash deploy/deploy.sh`. Use `SEED_DEMO=1` only when demo data is intentionally required. Never commit `.env`, API keys, SMTP credentials, or database passwords.
