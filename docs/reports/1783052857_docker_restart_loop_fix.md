# Docker Container Restart Loop Fix — Entry Point No Longer Crashes on Failures

**Report ID:** `1783052857_docker_restart_loop_fix`
**Date:** 2026-07-02
**Commit:** `b2d246d` (pushed to `origin/main`)
**Problem:** Container `necoyoad-next-app-1` stuck in restart loop — `docker compose exec` returns "Container is restarting"

---

## Root Cause

The `docker/entrypoint.sh` used `set -e` at the top, which causes the shell script to **exit immediately** if any command returns a non-zero exit code. When any of these commands failed:

1. `composer install` — if `composer.lock` is stale (doesn't include new packages like `intervention/image`, `enshrined/svg-sanitize`)
2. `php artisan migrate` — if MySQL isn't ready yet, or a migration has an error
3. `php artisan db:seed` — if a seeder throws an exception
4. `php artisan storage:link` — if the symlink already exists and can't be overwritten

...the script exited before reaching `exec "$@"` (line 65), so **FrankenPHP never started**. Docker's `restart: unless-stopped` policy then restarted the container, which crashed again → infinite restart loop.

The user saw:
```
Error response from daemon: Container b8aa095a83f7... is restarting, wait until the container is running
```

And the web server returned empty responses because FrankenPHP wasn't running.

---

## Fix

### 1. Removed `set -e` from `entrypoint.sh`

The script now **always reaches `exec "$@"`** even if migration/seed/composer-install fails. Every command has `|| echo WARNING` or `|| true` so failures are logged but don't stop the script.

### 2. Added error output (`2>&1`) to all artisan commands

So errors are visible in `docker compose logs app` instead of being swallowed.

### 3. Added cache clearing before FrankenPHP starts

```sh
php artisan config:clear 2>&1 || true
php artisan route:clear 2>&1 || true
php artisan view:clear 2>&1 || true
```

Stale config cache after code changes was a common cause of blank-page issues.

### 4. Dockerfile `composer install` is now non-fatal

```dockerfile
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts 2>&1 || \
    echo "WARNING: composer install failed at build time — will retry at runtime"
```

If `composer.lock` is stale or a package can't be resolved at build time, the image still builds. The entrypoint retries `composer install` at runtime.

### 5. Created media directories in entrypoint

```sh
mkdir -p storage/app/public/media/cache storage/app/public/media
```

So the FileManager disks are ready when the container starts.

---

## Files Changed (2 files, commit `b2d246d`)

- `docker/entrypoint.sh` — removed `set -e`, added `|| true` / `|| echo WARNING` to all commands, added cache clearing, added media dir creation
- `docker/Dockerfile` — composer install now non-fatal (`|| echo WARNING`)

---

## Recovery Steps

On your Windows machine:

```powershell
# 1. Pull the fix
git pull origin main

# 2. Stop the restart-looping container
docker compose down

# 3. Rebuild the image (Dockerfile changed)
docker compose build --no-cache app

# 4. Start fresh
docker compose up -d

# 5. Watch the boot sequence (should see all steps + "starting FrankenPHP")
docker compose logs -f app
```

You should see:
```
[entrypoint] creating .env from .env.example
[entrypoint] waiting for MySQL...
  [1/30] MySQL not ready yet, retrying in 2s...
[entrypoint] MySQL is up.
[entrypoint] running migrations...
[entrypoint] seeding database...
[entrypoint] starting FrankenPHP...
```

If any step fails, it shows `WARNING: ...` but **continues to FrankenPHP**.

### If composer install fails (stale lock file)

The `composer.lock` was committed before we added `intervention/image` + `enshrined/svg-sanitize`. You need to regenerate it:

```powershell
# After the container is running (even if composer install failed at boot):
docker compose exec app composer update intervention/image enshrined/svg-sanitize --no-interaction

# Or regenerate the entire lock file:
docker compose exec app composer update --no-interaction

# Then commit the updated lock file:
git add necoyoad-next/composer.lock
git commit -m "chore: update composer.lock with new packages"
git push origin main
```

### If you still see blank pages

```powershell
# Check if FrankenPHP is running
docker compose ps

# Check for Laravel errors
docker compose exec app tail -50 storage/logs/laravel.log

# Check the audit log
docker compose exec app tail -20 storage/logs/audit.log

# Verify the health endpoint
curl http://localhost:8080/up
```

With `APP_DEBUG=true` (set in `.env.example`), Laravel will show detailed error pages instead of blank screens.

---

## Why This Happened

The restart loop is a classic Docker anti-pattern: using `set -e` in an entrypoint that runs database migrations. Migrations can fail for legitimate reasons (DB not ready, schema already exists, etc.) — but the container should still start so you can debug.

The fix follows the principle: **the entrypoint's job is to start the application, not to guarantee migrations succeed**. Migrations are a best-effort operation; if they fail, the app still boots and you can run `php artisan migrate` manually to see the error.
