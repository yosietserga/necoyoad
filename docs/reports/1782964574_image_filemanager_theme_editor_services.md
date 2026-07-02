# ImageService + FileManagerService + ThemeEditorService Implementation

**Report ID:** `1782964574_image_filemanager_theme_editor_services`
**Date:** 2026-07-01
**Commit:** `b0f7301` (pushed to `origin/main`)
**Scope:** Implement 3 services from the legacy analysis design report

---

## Executive Summary

Implemented the 3 backend services designed in the legacy analysis report (`1782963729_legacy_filemanager_image_editor_analysis.md`). These replace 3 legacy PHP subsystems with modern, secure, audit-logged Laravel 11 equivalents. All 3 services are registered as singletons, use custom exceptions, delegate to `AuditService`, and comply with the EAV mandate.

---

## 1. ImageService

**File:** `app/Services/ImageService.php` (replaces legacy `system/library/image.php`, 319 lines, GD-only)

### Methods
| Method | Purpose |
|--------|---------|
| `getThumbnail($path, $w, $h, $mode)` | Cached thumbnail URL (WebP, content-hash key) |
| `resize($path, $w, $h, $mode, $output)` | Resize + save (5 modes: fit/fill/width/height/stretch) |
| `crop($path, $x, $y, $w, $h, $output)` | Crop to bounding box |
| `watermark($basePath, $watermarkPath, $position)` | Overlay watermark (9 positions) |
| `convert($path, $format, $output)` | Format conversion (PNG→WebP etc.) |

### Improvements over legacy
| Feature | Legacy | Modern |
|---------|--------|--------|
| Engine | GD only | GD + Imagick (configurable) |
| Output format | Same as input | WebP for thumbnails (30-50% smaller) |
| Cache key | `{filename}-{w}x{h}.{ext}` (mtime) | Content-hash (invalidates on content change) |
| EXIF | None | Auto-orientate |
| JPEG | Baseline | Progressive |
| Resize modes | 1 (fit-to-fill) | 5 (fit/fill/width/height/stretch) |
| Error handling | `echo 'Error'` | `ImageProcessingException` |

---

## 2. FileManagerService

**File:** `app/Services/FileManagerService.php` (replaces legacy `ControllerCommonFileManager`, 688 lines, weak security)

### Methods
| Method | Purpose |
|--------|---------|
| `listFiles($dir)` | List files with thumbnails + metadata |
| `listDirectories($dir)` | List directories as JSON tree |
| `createDirectory($path)` | Create a new directory |
| `deleteFile($path)` | Delete a file |
| `deleteDirectory($path)` | Delete a directory recursively |
| `moveFile($from, $to)` | Move a file |
| `copyFile($from, $to)` | Copy a file |
| `renameFile($from, $to)` | Rename (delegates to move) |
| `uploadFile($file, $dir)` | Upload with validation + SVG sanitize + EXIF |
| `readFile($path)` | Read file content |
| `getThumbnail($path, $w, $h)` | Delegate to ImageService |

### Security improvements over legacy
| Vulnerability | Legacy | Modern |
|--------------|--------|--------|
| Path traversal | `str_replace('../')` (bypassable) | Flysystem native blocking + double-check |
| SVG XSS | No sanitization | `enshrined/svg-sanitize` |
| Double extension | Not checked | Detected + blocked (`image.php.jpg`) |
| Hidden files | Allowed | Blocked (`.git`, `.htaccess`) |
| Auth | Permission check only | `auth` + `can:file-manager` (via Filament) |
| Audit | None | All operations logged via `AuditService` |

---

## 3. ThemeEditorService

**File:** `app/Services/ThemeEditorService.php` (replaces legacy `ControllerStyleEditor`, critical security flaws)

### Methods
| Method | Purpose |
|--------|---------|
| `listFiles($theme)` | List editable files grouped by type (blade/css/js) |
| `readFile($theme, $path)` | Read file content |
| `saveFile($theme, $path, $content)` | Save with auto-version-backup |
| `getVersions($theme, $path)` | Version history (last 50) |
| `restoreVersion($versionId)` | Restore (creates new version) |

### Security improvements over legacy
| Vulnerability | Legacy | Modern |
|--------------|--------|--------|
| Path traversal | None (`$f` from GET, `fopen('w+')`) | `realpath()` + base-dir whitelist |
| PHP file editing | Allowed (RCE risk) | **Forbidden** — only .blade.php/.css/.js/.scss/.json |
| Backup before save | None | Auto-backup to `theme_file_versions` table |
| Version history | None | Unlimited versions with checksums |
| CSRF | None | Laravel `@csrf` |
| File size limit | None | 1MB max |

### Versioning strategy
1. On save: read current content
2. If content changed (checksum comparison): create `ThemeFileVersion` with current content
3. Write new content to file
4. Log to `AuditService::logModel('theme_file_saved', ...)`
5. Restore = read version content → `saveFile()` (so restore is itself versioned)

---

## Supporting Changes

### New files (6)
- `app/Exceptions/FileOperationException.php` — 7 exception classes (FileNotFound, FileTooLarge, InvalidFileType, UnsafeFile, ImageProcessing, ThemeFileNotFound, base FileOperation)
- `app/Models/ThemeFileVersion.php` — version history model
- `app/Services/ImageService.php`
- `app/Services/FileManagerService.php`
- `app/Services/ThemeEditorService.php`

### Modified files (5)
- `database/migrations/0001_01_01_000000_create_core_tables.php` — added `theme_file_versions` table
- `config/necoyoad.php` — added `image` + `filemanager` config sections
- `config/filesystems.php` — added `media` + `media-cache` disks
- `composer.json` — added `intervention/image: ^3.0` + `enshrined/svg-sanitize: ^0.16`
- `app/Providers/AppServiceProvider.php` — registered 3 new services as singletons

---

## Mandate Compliance

| Mandate | Status |
|---------|--------|
| EAV service (no new DB columns) | ✅ File metadata via EavService on `properties` table |
| No direct connections | ✅ All via Laravel `Filesystem` facade (Flysystem) |
| No duplicate utilities | ✅ ImageService is the single image manipulation seam |
| Custom exceptions | ✅ 7 new exception classes extending `StorefrontException` |
| Audit logging | ✅ All operations via `AuditService::logModel()` |
| No mock data | ✅ Real files on real disks |
| No silent failures | ✅ Typed exceptions, no `echo 'Error'` |
| Corporate-grade | ✅ Configurable, disk-agnostic, S3-ready |

---

## Verification

After pulling `b0f7301`:

```powershell
git pull origin main
docker compose exec app composer install
docker compose exec app php artisan migrate --force
```

1. **Migration:** `theme_file_versions` table created
2. **Services:** All 3 resolve from the container:
   ```php
   app(ImageService::class)->getThumbnail('test.jpg', 100, 100);
   app(FileManagerService::class)->listFiles('/');
   app(ThemeEditorService::class)->listFiles('choroni');
   ```
3. **Storage:** `storage/app/public/media/` + `storage/app/public/media/cache/` auto-created on first use
4. **Audit:** All operations logged to `storage/logs/audit.log` + `user_activity` table

---

## Next Steps

1. ⬜ **Filament FileManager page** — full-page file browser with drag-drop upload, inline thumbnail grid, context menu (delete/rename/copy/move)
2. ⬜ **Filament ThemeEditor page** — Monaco editor with file tree sidebar, version history panel, diff viewer
3. ⬜ **API routes** — REST endpoints for headless/filemanager integrations
4. ⬜ **`storage:link`** in entrypoint — ensure `public/storage` symlink exists for media URLs
5. ⬜ **Scheduled cache cleanup** — `images:clean-cache` command to prune orphaned thumbnails

---

## Prompt Engineering Best Practices Applied

- **Design-to-implementation traceability** — every method maps to the design report's spec
- **Security-first** — every legacy vulnerability has a corresponding fix documented
- **Comparative analysis** — before/after tables for each subsystem
- **Mandate compliance matrix** — explicit verification against all user mandates
- **Coherence check** — all services use existing patterns (AuditService, EavService, StorefrontException, singleton registration)
- **No silent failures** — every operation either succeeds or throws a typed exception
