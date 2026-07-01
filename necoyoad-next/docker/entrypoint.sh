#!/bin/sh
set -e

cd /var/www/html

# 1. Ensure .env exists
if [ ! -f .env ]; then
    echo "[entrypoint] creating .env from .env.example"
    cp .env.example .env
fi

# 2. Force DB/Redis to the compose service names (overrides any localhost in .env)
sed -i 's/^DB_HOST=.*/DB_HOST=mysql/'        .env 2>/dev/null || true
sed -i 's/^DB_PORT=.*/DB_PORT=3306/'          .env 2>/dev/null || true
sed -i 's/^REDIS_HOST=.*/REDIS_HOST=redis/'   .env 2>/dev/null || true

# 3. Install composer deps if the bind mount hid them
if [ ! -f vendor/autoload.php ]; then
    echo "[entrypoint] installing composer dependencies..."
    composer install --no-dev --no-interaction --no-progress --optimize-autoloader
fi

# 4. App key
if ! grep -q "^APP_KEY=.\+" .env; then
    echo "[entrypoint] generating APP_KEY..."
    php artisan key:generate --force
fi

# 5. Writable dirs
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# 6. Wait for MySQL to accept connections before migrating
echo "[entrypoint] waiting for MySQL..."
for i in $(seq 1 30); do
    if php -r "try { new PDO('mysql:host=mysql;port=3306;', 'necoyoad', 'secret'); exit(0); } catch (\Throwable \$e) { exit(1); }" 2>/dev/null; then
        echo "[entrypoint] MySQL is up."
        break
    fi
    echo "  [$i/30] MySQL not ready yet, retrying in 2s..."
    sleep 2
done

# 7. Migrate
echo "[entrypoint] running migrations..."
php artisan migrate --force || echo "[entrypoint] migration skipped (DB not ready)"

# 8. Hand off to FrankenPHP / Caddy
exec "$@"
