<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ThemeFileNotFoundException;
use App\Exceptions\UnsafeFileException;
use App\Models\ThemeFileVersion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * ThemeEditorService — edit theme files (Blade, CSS, JS) with versioning.
 *
 * Replaces the legacy ControllerStyleEditor which had critical security flaws:
 *   - No path traversal protection ($f from GET, fopen('w+'))
 *   - PHP file editing allowed (RCE risk)
 *   - No backups before overwrite
 *   - No version history
 *   - No CSRF
 *
 * Modern design:
 *   - realpath() + whitelist of allowed base dirs
 *   - Only .blade.php, .css, .js editable (no PHP)
 *   - Auto-backup to theme_file_versions table before every save
 *   - Version diff + restore
 *   - File size limit (1MB)
 *   - Audit logging via AuditService
 *   - Custom exceptions (no silent failures)
 *
 * @see docs/reports/1782963729_legacy_filemanager_image_editor_analysis.md
 */
class ThemeEditorService
{
    private const MAX_FILE_SIZE = 1_048_576; // 1MB

    private const ALLOWED_EXTENSIONS = ['blade.php', 'css', 'js', 'scss', 'json'];

    public function __construct(
        private readonly AuditService $auditService,
    ) {}

    /**
     * List editable files in a theme, grouped by type.
     *
     * @return array<string, array<int, array{name: string, path: string, size: int}>>
     */
    public function listFiles(string $theme): array
    {
        $this->ensureSafeTheme($theme);
        $baseView = resource_path("views/themes/{$theme}");
        $baseAsset = resource_path("themes/{$theme}");

        $result = [
            'blade' => [],
            'css' => [],
            'js' => [],
        ];

        // Blade templates
        if (is_dir($baseView)) {
            $result['blade'] = $this->scanDirectory($baseView, ['blade.php'], $baseView);
        }

        // CSS + JS assets
        if (is_dir($baseAsset)) {
            $cssDir = $baseAsset . '/css';
            $jsDir = $baseAsset . '/js';
            if (is_dir($cssDir)) {
                $result['css'] = $this->scanDirectory($cssDir, ['css', 'scss'], $cssDir);
            }
            if (is_dir($jsDir)) {
                $result['js'] = $this->scanDirectory($jsDir, ['js'], $jsDir);
            }
        }

        return $result;
    }

    /**
     * Read a theme file's content.
     */
    public function readFile(string $theme, string $relativePath): string
    {
        $fullPath = $this->resolveSafePath($theme, $relativePath);

        if (!file_exists($fullPath)) {
            throw new ThemeFileNotFoundException($theme, $relativePath);
        }

        $this->validateFileSize($fullPath);

        $content = file_get_contents($fullPath);

        $this->auditService->logModel(
            event: 'theme_file_read',
            modelClass: 'ThemeFile',
            modelId: 0,
            changes: ['theme' => $theme, 'path' => $relativePath],
        );

        return $content;
    }

    /**
     * Save content to a theme file, creating a version backup first.
     */
    public function saveFile(string $theme, string $relativePath, string $content): void
    {
        $fullPath = $this->resolveSafePath($theme, $relativePath);

        // Validate content size
        if (strlen($content) > self::MAX_FILE_SIZE) {
            throw new UnsafeFileException('File content exceeds 1MB limit');
        }

        // Create version backup if file exists
        if (file_exists($fullPath)) {
            $currentContent = file_get_contents($fullPath);
            $currentChecksum = hash('sha256', $currentContent);
            $newChecksum = hash('sha256', $content);

            // Only create a version if content actually changed
            if ($currentChecksum !== $newChecksum) {
                ThemeFileVersion::create([
                    'theme' => $theme,
                    'file_path' => $relativePath,
                    'content' => $currentContent,
                    'user_id' => Auth::id(),
                    'checksum' => $currentChecksum,
                ]);
            } else {
                // Content unchanged — no-op
                return;
            }
        }

        // Ensure directory exists
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Write new content
        $written = file_put_contents($fullPath, $content);

        if ($written === false) {
            throw new UnsafeFileException("Failed to write file: {$relativePath}");
        }

        $this->auditService->logModel(
            event: 'theme_file_saved',
            modelClass: 'ThemeFile',
            modelId: 0,
            changes: [
                'theme' => $theme,
                'path' => $relativePath,
                'size' => $written,
                'checksum' => hash('sha256', $content),
            ],
        );
    }

    /**
     * Get version history for a theme file.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, ThemeFileVersion>
     */
    public function getVersions(string $theme, string $relativePath)
    {
        return ThemeFileVersion::where('theme', $theme)
            ->where('file_path', $relativePath)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();
    }

    /**
     * Restore a theme file to a previous version.
     */
    public function restoreVersion(int $versionId): void
    {
        $version = ThemeFileVersion::findOrFail($versionId);

        // The restore itself creates a new version (so restore is undoable)
        $this->saveFile($version->theme, $version->file_path, $version->content);

        $this->auditService->logModel(
            event: 'theme_file_restored',
            modelClass: 'ThemeFileVersion',
            modelId: $versionId,
            changes: [
                'theme' => $version->theme,
                'path' => $version->file_path,
            ],
        );
    }

    /**
     * Resolve a safe full path for a theme file, checking extension whitelist.
     */
    private function resolveSafePath(string $theme, string $relativePath): string
    {
        $this->ensureSafeTheme($theme);
        $this->ensureSafeRelativePath($relativePath);

        // Check extension whitelist
        $extension = $this->getExtension($relativePath);
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            throw new UnsafeFileException("File type '.{$extension}' is not editable. Allowed: " . implode(', ', self::ALLOWED_EXTENSIONS));
        }

        // Determine base directory based on extension
        if ($extension === 'blade.php') {
            $base = resource_path("views/themes/{$theme}");
        } elseif (in_array($extension, ['css', 'scss'])) {
            $base = resource_path("themes/{$theme}/css");
        } elseif ($extension === 'js') {
            $base = resource_path("themes/{$theme}/js");
        } else {
            $base = resource_path("themes/{$theme}");
        }

        $fullPath = realpath($base . '/' . dirname($relativePath)) . '/' . basename($relativePath);

        // Verify the resolved path is within the base directory
        $realBase = realpath($base);
        if ($realBase === false || !str_starts_with($fullPath, $realBase)) {
            throw new UnsafeFileException("Path resolves outside the theme directory: {$relativePath}");
        }

        return $fullPath;
    }

    /**
     * Scan a directory for files with allowed extensions.
     */
    private function scanDirectory(string $dir, array $extensions, string $base): array
    {
        $result = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }
            $ext = $this->getExtension($file->getFilename());
            if (!in_array($ext, $extensions)) {
                continue;
            }

            $relativePath = ltrim(str_replace($base, '', $file->getPathname()), '/');

            // Skip files larger than the limit
            if ($file->getSize() > self::MAX_FILE_SIZE) {
                continue;
            }

            $result[] = [
                'name' => $file->getFilename(),
                'path' => $relativePath,
                'size' => $file->getSize(),
            ];
        }

        usort($result, fn ($a, $b) => strcmp($a['path'], $b['path']));

        return $result;
    }

    /**
     * Get the extension from a filename (handles .blade.php double extension).
     */
    private function getExtension(string $filename): string
    {
        if (str_ends_with($filename, '.blade.php')) {
            return 'blade.php';
        }
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    /**
     * Validate file size.
     */
    private function validateFileSize(string $fullPath): void
    {
        $size = filesize($fullPath);
        if ($size === false || $size > self::MAX_FILE_SIZE) {
            throw new UnsafeFileException('File exceeds 1MB limit');
        }
    }

    /**
     * Ensure theme name is safe (alphanumeric + dashes only).
     */
    private function ensureSafeTheme(string $theme): void
    {
        if (!preg_match('/^[a-zA-Z0-9\-_]+$/', $theme)) {
            throw new UnsafeFileException("Invalid theme name: {$theme}");
        }
    }

    /**
     * Ensure relative path has no traversal attempts.
     */
    private function ensureSafeRelativePath(string $path): void
    {
        if (str_contains($path, '..') || str_contains($path, "\0") || str_starts_with($path, '/')) {
            throw new UnsafeFileException("Path traversal attempt: {$path}");
        }

        // Block hidden files
        $segments = explode('/', $path);
        foreach ($segments as $segment) {
            if ($segment !== '' && str_starts_with($segment, '.')) {
                throw new UnsafeFileException("Hidden file not allowed: {$path}");
            }
        }
    }
}
