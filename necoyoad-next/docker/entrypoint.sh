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

# 2. Force DB/Redis/session/cache/queue settings (bulletproof: replace OR add)
# Use a helper function that replaces the line if it exists, or appends if it doesn't
set_env() {
    local key="$1" val="$2"
    if grep -q "^${key}=" .env 2>/dev/null; then
        sed -i "s|^${key}=.*|${key}=${val}|" .env
    else
        echo "${key}=${val}" >> .env
    fi
}

set_env DB_HOST "mysql"
set_env DB_PORT "3306"
set_env DB_USERNAME "necoyoad"
set_env DB_PASSWORD "secret"
set_env REDIS_HOST "redis"
set_env REDIS_CLIENT "predis"
# CRITICAL: Use file-based session/cache to avoid Redis dependency in dev.
# Redis (predis) may not be installed yet — file driver always works.
set_env SESSION_DRIVER "file"
set_env SESSION_LIFETIME "120"
set_env SESSION_CONNECTION ""
set_env SESSION_STORE ""
set_env CACHE_STORE "file"
set_env QUEUE_CONNECTION "sync"
# Remove SESSION_CONNECTION if it was set to 'redis'
sed -i '/^SESSION_CONNECTION=$/d' .env 2>/dev/null || true

# 2b. Ensure storage directory structure exists and is writable
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache \
         storage/logs bootstrap/cache storage/app/public/media/cache \
         storage/app/public/media
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# 3. ALWAYS install/sync composer deps.
# The anonymous volume persists vendor/ across restarts, so without this,
# new packages (like predis/predis) are never installed.
# If composer.lock doesn't match composer.json, run composer update to regenerate.
echo "[entrypoint] syncing composer dependencies..."
composer install --no-dev --no-interaction --no-progress --optimize-autoloader 2>&1
if [ $? -ne 0 ]; then
    echo "[entrypoint] composer install failed — running composer update to regenerate lock..."
    composer update --no-dev --no-interaction --no-progress --optimize-autoloader 2>&1 || {
        echo "[entrypoint] WARNING: composer update also failed — app may not work correctly"
    }
fi
# Verify predis is actually installed (it's required for Redis config even with file sessions)
if [ ! -f vendor/predis/predis/src/Client.php ]; then
    echo "[entrypoint] predis not found — running composer require..."
    composer require predis/predis --no-interaction --no-progress 2>&1 || true
fi

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

# 7b. Publish Filament + Livewire assets (CSS/JS for admin panel)
echo "[entrypoint] publishing assets..."
php artisan vendor:publish --tag=filament-assets --force 2>&1 || true
php artisan vendor:publish --tag=laravel-assets --force 2>&1 || true
php artisan filament:assets 2>&1 || true
php artisan storage:link --force 2>&1 || true

# 8. Clear ALL caches (config, route, view, opcache) + delete cached files directly
# This is CRITICAL: if bootstrap/cache/config.php exists from a previous boot,
# Laravel uses the CACHED config (which may have SESSION_DRIVER=redis) instead
# of re-reading .env. php artisan config:clear may fail if composer just
# installed new packages, so we also delete the files directly.
rm -f bootstrap/cache/config.php bootstrap/cache/routes-v7.php bootstrap/cache/events.php bootstrap/cache/views.php 2>/dev/null || true
php artisan config:clear 2>&1 || true
php artisan route:clear 2>&1 || true
php artisan view:clear 2>&1 || true
# Clear opcache so PHP reads the latest files from the bind mount
php -r "if (function_exists('opcache_reset')) opcache_reset();" 2>/dev/null || true

# 8b. Verify the session driver is actually 'file' (debug output)
echo "[entrypoint] SESSION_DRIVER=$(grep '^SESSION_DRIVER=' .env | cut -d'=' -f2-)"

# 9. Hand off to FrankenPHP / Caddy (MUST always reach here)
echo "[entrypoint] starting FrankenPHP..."
exec "$@"
