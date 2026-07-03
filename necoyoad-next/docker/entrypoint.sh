#!/bin/sh
# IMPORTANT: Do NOT use `set -e` here. If any artisan command fails (e.g.,
# DB not ready, migration error), the script must still reach `exec "$@"`
# so FrankenPHP starts and the container doesn't enter a restart loop.
# Docker's restart policy will keep restarting if the entrypoint exits non-zero.

cd /var/www/html

# 1. Ensure .env exists
if [ ! -f .env ]; then
    echo "[entrypoint] creating .env from .env.example"
    cp .env.example .env
fi

# 2. Force DB/Redis to the compose service names (overrides any localhost in .env)
sed -i 's/^DB_HOST=.*/DB_HOST=mysql/'            .env 2>/dev/null || true
sed -i 's/^DB_PORT=.*/DB_PORT=3306/'              .env 2>/dev/null || true
sed -i 's/^DB_USERNAME=.*/DB_USERNAME=necoyoad/'  .env 2>/dev/null || true
sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=secret/'    .env 2>/dev/null || true
sed -i 's/^REDIS_HOST=.*/REDIS_HOST=redis/'       .env 2>/dev/null || true
sed -i 's/^SESSION_DRIVER=.*/SESSION_DRIVER=file/' .env 2>/dev/null || true
sed -i 's/^CACHE_STORE=.*/CACHE_STORE=file/'       .env 2>/dev/null || true
sed -i 's/^QUEUE_CONNECTION=.*/QUEUE_CONNECTION=sync/' .env 2>/dev/null || true

# 2b. Ensure storage directory structure exists and is writable
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache \
         storage/logs bootstrap/cache storage/app/public/media/cache \
         storage/app/public/media
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# 3. Install composer deps if the bind mount hid them
if [ ! -f vendor/autoload.php ]; then
    echo "[entrypoint] installing composer dependencies..."
    # Try composer install first (uses lock file). If the lock file is stale
    # (doesn't match composer.json), fall back to composer update which
    # regenerates the lock file with the packages from composer.json.
    composer install --no-dev --no-interaction --no-progress --optimize-autoloader 2>&1 || {
        echo "[entrypoint] composer install failed (stale lock file?) — running composer update..."
        composer update --no-dev --no-interaction --no-progress --optimize-autoloader 2>&1 || {
            echo "[entrypoint] WARNING: composer update also failed — app may not work correctly"
        }
    }
fi

# 4. App key — ensure APP_KEY is set and non-empty
APP_KEY_VAL=$(grep "^APP_KEY=" .env | cut -d'=' -f2- | tr -d '[:space:]')
if [ -z "$APP_KEY_VAL" ] || [ "$APP_KEY_VAL" = "base64:" ]; then
    echo "[entrypoint] generating APP_KEY (current value is empty or invalid)..."
    # Try artisan first (writes to .env)
    php artisan key:generate --force 2>&1 || {
        # Fallback: generate manually if artisan fails
        echo "[entrypoint] artisan key:generate failed, generating manually..."
        RAW_KEY=$(php -r "echo base64_encode(random_bytes(32));")
        sed -i "s/^APP_KEY=.*/APP_KEY=base64:${RAW_KEY}/" .env 2>/dev/null || \
            echo "APP_KEY=base64:${RAW_KEY}" >> .env
    }
    echo "[entrypoint] APP_KEY generated."
fi

# 5. Writable dirs + storage symlink (for media disk public URLs)
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
php artisan storage:link --force 2>&1 || true

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

# 7. Migrate + seed (NON-FATAL — if these fail, the container still starts)
echo "[entrypoint] running migrations..."
php artisan migrate --force 2>&1 || echo "[entrypoint] WARNING: migration failed — check logs"

echo "[entrypoint] seeding database..."
php artisan db:seed --force 2>&1 || echo "[entrypoint] WARNING: seeding failed — may already be seeded"

# 8. Clear any cached config that might be stale after code changes
php artisan config:clear 2>&1 || true
php artisan route:clear 2>&1 || true
php artisan view:clear 2>&1 || true

# 9. Hand off to FrankenPHP / Caddy (MUST always reach here)
echo "[entrypoint] starting FrankenPHP..."
exec "$@"
