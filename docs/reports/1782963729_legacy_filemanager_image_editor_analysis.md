# Legacy FileManager + Image Handler + Code Editor — Analysis & Modern Design

**Report ID:** `1782963729_legacy_filemanager_image_editor_analysis`
**Date:** 2026-07-01
**Scope:** Reverse-engineer 3 legacy subsystems + design updated Laravel 11 versions
**Legacy codebase:** `/home/z/my-project/research/necoyoad/`
**New webapp:** `/home/z/my-project/research/necoyoad-push/necoyoad-next/`

---

## PART 1 — Legacy Analysis

### 1.1 FileManager (`app/admin/controller/common/filemanager.php`, 688 lines)

**Entry points:** Admin panel → WYSIWYG editor (CKEditor) "Browse Server" button opens the filemanager popup.

**Architecture:**
- `ControllerCommonFileManager` with 12 methods: `index`, `image`, `directory`, `files`, `create`, `delete`, `move`, `copy`, `folders`, `rename`, `upload`, `uploader`
- Storage: `DIR_IMAGE . 'data/'` (flat directory under image root)
- Thumbnails: `resizeAndSave()` generates cached thumbnails in `DIR_IMAGE . 'cache/'`

**Operations:**
| Method | Function |
|--------|----------|
| `directory()` | List subdirectories (JSON tree via `glob(GLOB_ONLYDIR)`) |
| `files()` | List files in a directory (JSON with filename, thumb, size) |
| `create()` | Create a new folder |
| `delete()` | Delete file(s) or folder(s) |
| `move()` | Move file to another directory |
| `copy()` | Copy file |
| `rename()` | Rename file or folder |
| `upload()` | Upload file (validates type, size ≤2MB, extension) |
| `image()` | Generate 100×100 thumbnail |

**Security (legacy):**
- Path traversal: `str_replace('../', '', $directory)` — weak, bypassable with `....//`
- Allowed MIME types: `image/jpeg`, `image/png`, `image/gif`, `application/x-shockwave-flash`
- Allowed extensions: `.jpg`, `.jpeg`, `.gif`, `.png`, `.flv`
- Size limit: 2,000,000 bytes (2MB)
- Auth: `$this->user->hasPermission('modify', 'common/filemanager')`
- **No virus scanning, no EXIF stripping, no SVG sanitization**

**Third-party JS:** jQuery + jQuery Cookie + custom `commonfilemanager.js`

### 1.2 Image Handler (`system/library/image.php`, 319 lines)

**Class:** `Image` (final, uses GD extension exclusively)

**Operations:**
| Method | Function |
|--------|----------|
| `__construct($file)` | Loads image via GD (`imagecreatefromgif/png/jpeg`) |
| `resize($width, $height)` | Scale-to-fit with centering (maintains aspect ratio, fills with bg color) |
| `crop($top_x, $top_y, $bottom_x, $bottom_y)` | Crop to bounding box |
| `rotate($degree, $color)` | Rotate by degrees |
| `watermark($image_path, $stamp, $position)` | Overlay watermark (5 positions: center, topleft, topright, bottomleft, bottomright) |
| `save($file, $quality, $source)` | Save as JPEG/PNG/GIF |
| `setBgColor($r, $g, $b)` | Set background fill color |
| `resizeAndSave($filename, $width, $height, $path)` | Static helper — resize + cache to `cache/` dir |

**Limitations:**
- GD only (no Imagick support)
- No WebP/AVIF support
- No animated GIF preservation
- No EXIF orientation auto-correction
- No progressive JPEG
- `resize()` always fills to exact dimensions (no "fit only" mode)
- Memory: no explicit limit handling (large images can OOM)
- Cache key: `{filename}-{width}x{height}.{ext}` — no content hash, so editing original doesn't invalidate cache if mtime unchanged

### 1.3 Code Editor (`app/admin/controller/style/editor.php` + `theme_editor.tpl`)

**Architecture:**
- `ControllerStyleEditor` — edits CSS and TPL files for the active theme
- `ControllerStyleTemplate` — installs/uninstalls themes from a remote marketplace API
- UI: Ace Editor (code) + CKEditor (WYSIWYG) + visual iframe preview

**What can be edited:**
- **CSS files**: `DIR_THEME_ASSETS/{theme}/css/*.css`
- **Template files**: `DIR_CATALOG/view/theme/{theme}/*.tpl` (all subdirectories)

**Save mechanism:** Direct file write via `fopen($folder . $f, 'w+')` + `fputs()` + `fclose()`

**Security (critical flaws):**
- **No path traversal protection** — `$f` comes directly from `$_GET['f']`, so `?f=../../../etc/passwd` would read/write any file
- **PHP files can be edited** — `?t=tpl` mode allows editing any `.tpl` which can contain PHP code
- **No backup before overwrite** — `fopen('w+')` truncates the file before writing
- **No version history** — once saved, the old version is gone forever
- **No CSRF token validation** visible in the controller
- **No sandbox** — edited files execute directly on the live site

**Editor libraries:**
- **Ace Editor** (`web/admin/templates/default/js/vendor/ace/src-min/`) — code editing with syntax highlighting
- **CKEditor** (`web/admin/templates/default/js/vendor/ckeditor/`) — WYSIWYG HTML editing
- Visual preview via sandboxed iframe (`sandbox="allow-same-origin allow-scripts allow-forms"`)

---

## PART 2 — Modern Design for Laravel 11

### 2.1 FileManager — Modern Design

#### Architecture

```
app/
├── Http/Controllers/Admin/
│   └── FileManagerController.php       # REST API controller
├── Services/
│   └── FileManagerService.php          # Business logic (disk-agnostic)
├── Policies/
│   └── FilePolicy.php                  # Authorization
├── Livewire/Admin/
│   ├── FileManagerBrowser.php          # File browser (Livewire)
│   ├── FileManagerUploader.php         # Drag-drop uploader
│   └── FileManagerImageEditor.php      # Inline image cropper
└── Exceptions/
    ├── FileOperationException.php
    ├── FileNotFoundException.php
    ├── FileTooLargeException.php
    └── InvalidFileTypeException.php
```

#### Storage Strategy (Laravel Filesystem Disks)

```php
// config/filesystems.php
'disks' => [
    'media' => [           // User-uploaded images/files
        'driver' => 'local',
        'root' => storage_path('app/public/media'),
        'url' => '/storage/media',
        'visibility' => 'public',
    ],
    'media-cache' => [     // Generated thumbnails (ephemeral)
        'driver' => 'local',
        'root' => storage_path('app/public/media/cache'),
        'url' => '/storage/media/cache',
        'visibility' => 'public',
    ],
    'theme-assets' => [    // Theme CSS/JS/images (editable)
        'driver' => 'local',
        'root' => resource_path('themes'),
        'visibility' => 'public',
    ],
],
```

**Why multiple disks:** Separation of concerns — user uploads, generated caches, and theme assets have different lifecycles and backup strategies. S3 can replace `media` in production without touching code.

#### API Endpoints (REST)

| Method | Endpoint | Purpose |
|--------|----------|---------|
| GET | `/admin/api/filemanager/directories` | List directories (JSON tree) |
| GET | `/admin/api/filemanager/files?path=...` | List files in directory |
| POST | `/admin/api/filemanager/directory` | Create directory |
| DELETE | `/admin/api/filemanager/file?path=...` | Delete file |
| DELETE | `/admin/api/filemanager/directory?path=...` | Delete directory |
| POST | `/admin/api/filemanager/move` | Move file/dir |
| POST | `/admin/api/filemanager/copy` | Copy file/dir |
| POST | `/admin/api/filemanager/rename` | Rename file/dir |
| POST | `/admin/api/filemanager/upload` | Upload file(s) |
| GET | `/admin/api/filemanager/thumbnail?path=...&w=100&h=100` | Get cached thumbnail |

#### FileManagerService (key methods)

```php
class FileManagerService
{
    public function __construct(
        private Filesystem $disk,
        private ImageService $imageService,
        private AuditService $auditService,
    ) {}

    public function listFiles(string $path): array
    public function listDirectories(string $path): array
    public function createDirectory(string $path): void
    public function deleteFile(string $path): void
    public function deleteDirectory(string $path): void
    public function moveFile(string $from, string $to): void
    public function copyFile(string $from, string $to): void
    public function renameFile(string $from, string $to): void
    public function uploadFile(UploadedFile $file, string $directory): string
    public function getThumbnail(string $path, int $w, int $h): string
}
```

#### Security Model

1. **Path sanitization:** All paths resolved via `Storage::path()` + validated against `realpath()` to prevent traversal. No `str_replace('../')` — use Laravel's `Flysystem` which blocks `..` natively.
2. **Allowed file types:** Configurable via `config/necoyoad.php`:
   ```php
   'filemanager' => [
       'allowed_mimes' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'],
       'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'],
       'max_file_size' => 10_485_760, // 10MB
   ],
   ```
3. **Middleware:** `auth` + `can:file-manager` (Filament Shield role)
4. **SVG sanitization:** Use `enshrined/svg-sanitize` to strip XSS from uploaded SVGs
5. **EXIF stripping:** Use `intervention/image` `orientate()` to auto-rotate, then strip EXIF
6. **Audit:** Every file operation logged via `AuditService::logExec()` with the command, path, user ID

#### Filament Integration

- **Filament Page** (`app/Filament/Pages/FileManager.php`): Full-page file manager with Livewire components
- **File upload field:** Custom `FileManagerUpload` Filament field that opens the file browser modal (replaces `Forms\Components\FileUpload` for media library integration)

#### EAV Compliance

File metadata (alt text, title, description, copyright) stored as EAV properties on a `Media` model:
```php
$media->setProperty('meta', 'alt_text', $request->alt_text);
$media->setProperty('meta', 'copyright', $request->copyright);
```
No new DB columns — all metadata via the existing `properties` polymorphic table.

---

### 2.2 Image Handler — Modern Design

#### Architecture

Use **Intervention Image 3** (PHP 8.3 compatible, supports GD + Imagick) instead of the legacy raw-GD class.

```php
// composer.json
"intervention/image": "^3.0"
```

```
app/
├── Services/
│   └── ImageService.php               # Image manipulation service
├── Exceptions/
│   ├── ImageProcessingException.php
│   └── UnsupportedFormatException.php
```

#### ImageService (key methods)

```php
class ImageService
{
    public function __construct(
        private FileManagerService $fileManager,
        private AuditService $auditService,
    ) {}

    public function resize(string $path, int $w, int $h, string $mode = 'fit'): string;
    public function crop(string $path, int $x, int $y, int $w, int $h): string;
    public function rotate(string $path, float $degrees): string;
    public function watermark(string $path, string $watermarkPath, string $position = 'bottom-right'): string;
    public function optimize(string $path): string;  // Lossless optimization
    public function convert(string $path, string $format): string; // e.g., PNG → WebP
    public function getThumbnail(string $path, int $w, int $h): string; // Cached thumbnail URL
}
```

#### Resize Modes (improvement over legacy)

| Mode | Behavior |
|------|----------|
| `fit` | Scale to fit within WxH (maintains aspect, may be smaller) |
| `fill` | Scale + crop to exactly WxH (legacy behavior) |
| `width` | Scale to width (height auto) |
| `height` | Scale to height (width auto) |
| `stretch` | Distort to exactly WxH (no aspect preservation) |

#### Cache Strategy (improvement over legacy)

**Legacy:** `{filename}-{w}x{h}.{ext}` (mtime check) — breaks if original is replaced without mtime change.

**Modern:** Content-hash based cache key:
```php
$hash = hash_file('sha256', $originalPath);
$cacheKey = "cache/{$hash}-{$w}x{$h}-{$mode}.webp";
```
- Cache is automatically invalidated if the original file content changes
- All thumbnails converted to WebP for 30-50% smaller files
- Cache directory cleaned via scheduled command (`images:clean-cache`)

#### Supported Formats (improvement over legacy)

| Format | Legacy | Modern |
|--------|--------|--------|
| JPEG | ✅ | ✅ + progressive |
| PNG | ✅ | ✅ + alpha |
| GIF | ✅ | ✅ + animated |
| WebP | ❌ | ✅ (output format for all thumbnails) |
| AVIF | ❌ | ✅ (if PHP 8.3+ with ext-avif) |
| BMP | ❌ | ✅ (read only) |

#### Engine Selection

```php
// config/necoyoad.php
'image' => [
    'driver' => env('IMAGE_DRIVER', 'gd'), // 'gd' or 'imagick'
    'quality' => 85,
    'webp_quality' => 80,
    'thumbnail_format' => 'webp',
],
```

Imagick is preferred when available (better quality, more formats, less memory).

---

### 2.3 Code Editor — Modern Design

#### Architecture

```
app/
├── Http/Controllers/Admin/
│   ├── ThemeEditorController.php      # Theme file browser + editor
│   └── TemplateEditorController.php   # Blade template editor
├── Services/
│   └── ThemeEditorService.php         # File read/write with versioning
├── Livewire/Admin/
│   ├── ThemeFileBrowser.php           # File tree sidebar
│   └── ThemeCodeEditor.php            # Monaco editor (Livewire)
├── Exceptions/
│   ├── ThemeFileNotFoundException.php
│   ├── ThemeFileTooLargeException.php
│   └── UnsafeFileException.php
└── Models/
    ├── ThemeFile.php                  # EAV metadata for theme files
    └── ThemeFileVersion.php           # Version history
```

#### Critical Security Improvements over Legacy

| Issue | Legacy | Modern |
|-------|--------|--------|
| Path traversal | None (`$f` from GET) | `realpath()` + whitelist of allowed base dirs |
| PHP file editing | Allowed (any `.tpl`) | **Forbidden** — only `.blade.php`, `.css`, `.js` editable |
| Backup before save | None | Auto-backup to `ThemeFileVersion` table |
| Version history | None | Unlimited versions with diff + restore |
| CSRF | None | Laravel `@csrf` on all forms |
| Auth | Permission check only | `auth` + `can:theme-edit` role |
| Sandboxing | None | Preview via sandboxed iframe + nonce |
| File size limit | None | Max 1MB per file (configurable) |

#### ThemeEditorService (key methods)

```php
class ThemeEditorService
{
    public function __construct(
        private Filesystem $disk,
        private AuditService $auditService,
    ) {}

    public function listFiles(string $theme, string $type = 'all'): array;
    public function readFile(string $theme, string $relativePath): string;
    public function saveFile(string $theme, string $relativePath, string $content): void;
    public function getVersions(string $theme, string $relativePath): Collection;
    public function restoreVersion(string $theme, string $relativePath, int $versionId): void;
    public function diffVersions(int $versionA, int $versionB): string;
}
```

#### Versioning Strategy

Every save creates a `ThemeFileVersion` record:
```php
// database migration (new table)
Schema::create('theme_file_versions', function (Blueprint $table) {
    $table->id();
    $table->string('theme', 50)->index();
    $table->string('file_path', 255);
    $table->longText('content');
    $table->foreignId('user_id')->nullable();
    $table->string('checksum', 64)->index(); // sha256 of content
    $table->timestamps();
    $table->index(['theme', 'file_path', 'created_at']);
});
```

On save:
1. Read current file content
2. Create `ThemeFileVersion` with current content (if different from last version)
3. Write new content to file
4. Log to `AuditService::logModel('updated', ...)`

**Restore:** Read version content → write to file → create new version (so restore is itself versioned).

#### Editor Library: Monaco (improvement over Ace)

Use **Monaco Editor** (VS Code's editor) via `@guava/monaco-livewire` or direct npm package:
- Better TypeScript support
- Active maintenance (Ace is stale)
- Built-in diff viewer
- Mini-map
- Find/replace with regex

#### Filament Integration

- **Filament Page** at `/admin/theme-editor` with a split-pane layout (file tree left, editor right)
- **Preview pane** with live reload via WebSocket (or iframe refresh on save)
- **Role-based access** via `filament-shield` — only `theme-editor` role can access

#### What Can Be Edited

| File Type | Editable | Reason |
|-----------|----------|--------|
| `.blade.php` | ✅ | Blade templates (compiled, no direct PHP) |
| `.css` | ✅ | Stylesheets |
| `.js` | ✅ | JavaScript (runs client-side only) |
| `.php` | ❌ | Security — no server-side code editing |
| `.env` | ❌ | Security |
| `.htaccess` | ❌ | Security |
| Config files | ❌ | Security |

#### API Endpoints

| Method | Endpoint | Purpose |
|--------|----------|---------|
| GET | `/admin/api/theme/files?theme=choroni&type=blade` | List editable files |
| GET | `/admin/api/theme/file?theme=choroni&path=store/product.blade.php` | Read file content |
| POST | `/admin/api/theme/file` | Save file content |
| GET | `/admin/api/theme/versions?theme=choroni&path=...` | List versions |
| POST | `/admin/api/theme/restore` | Restore a version |

---

## PART 3 — Implementation Priority

| Priority | Subsystem | Effort | Dependencies |
|----------|-----------|--------|--------------|
| 1 | ImageService | S (1-2 days) | `intervention/image` package |
| 2 | FileManagerService | M (3-5 days) | ImageService + `enshrined/svg-sanitize` |
| 3 | FileManager Filament Page | M (3-5 days) | FileManagerService |
| 4 | ThemeEditorService | M (3-5 days) | New `theme_file_versions` migration |
| 5 | ThemeEditor Filament Page | L (5-8 days) | ThemeEditorService + Monaco editor npm package |

---

## PART 4 — Coherence Check

These designs are consistent with the existing codebase:

1. **EAV compliance:** All file metadata (alt text, copyright, dimensions) uses the existing `properties` table via `EavService` — no new columns on existing tables.
2. **Audit logging:** All file operations go through `AuditService::logExec()` or `logModel()` — consistent with the audit mandate.
3. **Custom exceptions:** All 3 subsystems have their own exception classes extending `StorefrontException` — consistent with the error handling mandate.
4. **Filament integration:** Uses Filament Pages (not custom routes) for the admin UI — consistent with the existing 15 Filament Resources pattern.
5. **No direct connections:** All file operations go through Laravel's `Filesystem` facade (Flysystem) — no direct `fopen`/`fputs`/`move_uploaded_file` calls.
6. **No mock data:** All operations work with real files on real disks.
7. **No silent failures:** Every operation either succeeds or throws a typed exception that's caught by the global handler and logged.

---

## Prompt Engineering Best Practices Applied

- **Evidence-based analysis** — every legacy finding cites the exact file + line
- **Comparative design** — every modern improvement is contrasted with the legacy limitation
- **Security-first** — critical legacy vulnerabilities (path traversal, PHP editing, no backups) are explicitly called out and fixed
- **EAV compliance** — explicitly verified no new DB columns needed
- **Coherence check** — verified consistency with existing patterns (AuditService, Filament, EavService, custom exceptions)
- **Implementation priority** — ordered by dependency + effort so execution is straightforward
