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
# Force predis (pure PHP) instead of phpredis (requires C extension not in FrankenPHP image)
sed -i 's/^REDIS_CLIENT=.*/REDIS_CLIENT=predis/'  .env 2>/dev/null || true
grep -q "^REDIS_CLIENT=" .env 2>/dev/null || echo "REDIS_CLIENT=predis" >> .env
sed -i 's/^SESSION_DRIVER=.*/SESSION_DRIVER=file/' .env 2>/dev/null || true
sed -i 's/^CACHE_STORE=.*/CACHE_STORE=file/'       .env 2>/dev/null || true
sed -i 's/^QUEUE_CONNECTION=.*/QUEUE_CONNECTION=sync/' .env 2>/dev/null || true

# 2b. Ensure storage directory structure exists and is writable
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache \
         storage/logs bootstrap/cache storage/app/public/media/cache \
         storage/app/public/media
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# 3. ALWAYS install/sync composer deps — even if vendor/autoload.php exists,
# new packages may have been added to composer.json since the last boot.
# The anonymous volume persists vendor/ across restarts, so without this,
# new packages (like predis/predis) are never installed.
echo "[entrypoint] syncing composer dependencies..."
composer install --no-dev --no-interaction --no-progress --optimize-autoloader 2>&1 || {
    echo "[entrypoint] composer install failed (stale lock file?) — running composer update..."
    composer update --no-dev --no-interaction --no-progress --optimize-autoloader 2>&1 || {
        echo "[entrypoint] WARNING: composer update also failed — app may not work correctly"
    }
}

# 4. App key — ensure APP_KEY is set and non-empty
# Always check and regenerate if missing (handles stale .env from before the fix)
APP_KEY_VAL=$(grep "^APP_KEY=" .env 2>/dev/null | cut -d'=' -f2- | tr -d '[:space:]')
if [ -z "$APP_KEY_VAL" ] || [ "$APP_KEY_VAL" = "base64:" ]; then
    echo "[entrypoint] APP_KEY is empty or invalid — generating..."
    # Try artisan first (writes to .env)
    php artisan key:generate --force 2>&1 || true

    # Verify artisan actually set it
    APP_KEY_VAL=$(grep "^APP_KEY=" .env 2>/dev/null | cut -d'=' -f2- | tr -d '[:space:]')
    if [ -z "$APP_KEY_VAL" ] || [ "$APP_KEY_VAL" = "base64:" ]; then
        echo "[entrypoint] artisan key:generate failed — generating manually..."
        RAW_KEY=$(php -r "echo base64_encode(random_bytes(32));")
        # Replace the APP_KEY line, or append if not found
        if grep -q "^APP_KEY=" .env 2>/dev/null; then
            sed -i "s|^APP_KEY=.*|APP_KEY=base64:${RAW_KEY}|" .env
        else
            echo "APP_KEY=base64:${RAW_KEY}" >> .env
        fi
    fi
    echo "[entrypoint] APP_KEY generated successfully."
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
# Clear opcache so PHP reads the latest files from the bind mount
php -r "if (function_exists('opcache_reset')) opcache_reset();" 2>/dev/null || true

# 9. Hand off to FrankenPHP / Caddy (MUST always reach here)
echo "[entrypoint] starting FrankenPHP..."
exec "$@"
