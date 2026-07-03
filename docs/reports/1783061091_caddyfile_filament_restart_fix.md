# Container Restart Loop — Caddyfile Regex + Filament Visibility Fixes

**Report ID:** `1783061091_caddyfile_filament_restart_fix`
**Date:** 2026-07-02
**Commit:** `e01dbb0` (pushed to `origin/main`)
**Problem:** Container stuck in restart loop with two FATAL errors

---

## Root Causes (2 FATAL bugs)

### Bug 1: Caddyfile regex error (FrankenPHP failed to start)

**Error from logs:**
```
Error: loading initial config: loading new config: loading http app module:
provision http: server srv0: setting up route matchers: route 4: loading
matcher modules: module name 'path_regexp': provision http.matchers.path_regexp:
compiling matcher regexp \/\.(?!well-known): error parsing regexp:
invalid or unsupported Perl syntax: `(?!`
```

**Root cause:** Caddy uses Go's RE2 regexp engine, which **does not support Perl lookahead** `(?!well-known)`. The `path_regexp` directive with this pattern causes Caddy to fail loading the config → FrankenPHP never starts → container exits code 1 → Docker restarts → infinite loop.

**Fix:** Replaced the regex with Caddy's simple `path` matcher using glob patterns:
```caddyfile
@hidden path /.*.env* /.git* /.htaccess* /.DS_Store
respond @hidden 404
```
No regex needed — glob patterns are sufficient for blocking hidden files.

### Bug 2: Filament shouldRegisterNavigation visibility (PHP fatal)

**Error from logs:**
```
Access level to App\Filament\Pages\FileManager::shouldRegisterNavigation()
must be public (as in class Filament\Pages\Page)
```

**Root cause:** `FileManager.php` and `ThemeEditor.php` declared `shouldRegisterNavigation()` as `protected static`, but Filament's parent `Page` class declares it as `public static`. PHP throws a fatal error when a child class reduces visibility of a parent method. This caused every `php artisan` command (migrate, db:seed, config:clear) to fail because the Filament service provider couldn't boot.

**Fix:** Changed `protected static function shouldRegisterNavigation()` → `public static function shouldRegisterNavigation()` on both pages.

---

## Files Changed (3 files, commit `e01dbb0`)

| File | Change |
|------|--------|
| `Caddyfile` | Replaced `path_regexp \/\.(?!well-known)` with `path` glob matcher |
| `app/Filament/Pages/FileManager.php` | `protected static` → `public static` |
| `app/Filament/Pages/ThemeEditor.php` | `protected static` → `public static` |

---

## Recovery Steps

On your Windows machine:

```powershell
# 1. Pull the fix
git pull origin main

# 2. Stop the loop
docker compose down

# 3. Rebuild (Caddyfile is baked into the image)
docker compose build --no-cache app

# 4. Start fresh
docker compose up -d

# 5. Watch the boot — should now reach "starting FrankenPHP" and stay running
docker compose logs -f app
```

Expected output:
```
[entrypoint] waiting for MySQL...
[entrypoint] MySQL is up.
[entrypoint] running migrations...
INFO  Nothing to migrate.
[entrypoint] seeding database...
INFO  Seeding database.
[entrypoint] starting FrankenPHP...
{"level":"info","msg":"using provided configuration",...}
# FrankenPHP stays running — no "exited with code 1"
```

### Verify

```powershell
# Container should be "Up" (not "Restarting")
docker compose ps

# Healthcheck should return 200
curl http://localhost:8080/up

# Homepage should render
curl -s http://localhost:8080/ | head -20

# Admin should load
curl -s http://localhost:8080/admin | head -10
```

---

## Why Both Bugs Were Missed

1. **Caddyfile regex** — I wrote the regex with Perl lookahead (common in nginx/Apache configs) but didn't account for Go's RE2 engine limitations. The fix is a simple glob pattern instead.

2. **Filament visibility** — Filament's `Page` class declares `shouldRegisterNavigation()` as `public static`, but I wrote it as `protected static` (a common PHP habit for internal methods). PHP's strict visibility inheritance rules caught this at class-load time.

Both bugs only surface when the container actually runs — they can't be detected by static analysis alone without a PHP runtime + Caddy in the loop.
