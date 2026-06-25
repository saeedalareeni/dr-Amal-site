# Amal Aleissa CMS

Laravel 12 CMS for the bilingual Amal Aleissa portfolio. The public site is Arabic at `/`, English at `/en`, and the protected administration area is at `/admin`.

## Local setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build
```

The local seed account uses `ADMIN_EMAIL` and `ADMIN_PASSWORD`. The administrator is required to replace the initial password after login.

## Hostinger Shared deployment

1. Point the domain document root to Laravel's `public` directory. If Hostinger forces `public_html`, place the public entry files there and update `index.php` paths to the application directory.
2. Use PHP 8.2 or newer with `pdo_mysql`, `mbstring`, `fileinfo`, `openssl`, and `gd` enabled.
3. Create a MySQL database, fill `DB_*`, and set `APP_URL`, `APP_ENV=production`, `APP_DEBUG=false`, `APP_TIMEZONE=Asia/Riyadh`.
4. Configure the single Hostinger mailbox through `MAIL_HOST`, `MAIL_PORT`, `MAIL_SCHEME`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`, and `MAIL_ADMIN_ADDRESS`. Never commit `.env`.
5. Run `php artisan migrate --force`, `php artisan storage:link`, `npm run build`, then `php artisan optimize`.
6. Add a cron job every minute:

```bash
cd /home/USER/path/to/app && php artisan queue:work --stop-when-empty --tries=3 --timeout=90 >> /dev/null 2>&1
```

Back up both MySQL and `storage/app` before every deployment. Files offered to subscribers live on the private disk and are only served through signed routes.

## Quality checks

```bash
php artisan test
npm run build
npm run test:e2e
```

On this Windows/Laragon installation SQLite is available but disabled in `php.ini`; the Pest suite can be run without changing global configuration using:

```bash
php -d extension=php_pdo_sqlite.dll -d extension=php_sqlite3.dll vendor/pestphp/pest/bin/pest --compact
```

Use `php artisan cms:import-media` if the legacy source folders need to be reverified before cleanup.
