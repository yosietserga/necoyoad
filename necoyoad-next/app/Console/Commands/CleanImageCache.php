<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * CleanImageCache — prunes orphaned/stale thumbnails from the media-cache disk.
 *
 * Thumbnails are content-hash keyed, so when a source image is deleted or
 * modified, the old thumbnail becomes orphaned. This command removes:
 *   1. Thumbnails whose source file no longer exists
 *   2. Thumbnails older than the configured TTL (default: 30 days)
 *
 * Scheduled daily via routes/console.php.
 */
class CleanImageCache extends Command
{
    protected $signature = 'images:clean-cache {--days=30 : Remove thumbnails older than N days}';
    protected $description = 'Clean orphaned and stale image thumbnails from the media-cache disk';

    public function handle(): int
    {
        $cacheDir = storage_path('app/public/media/cache');
        $mediaDir = storage_path('app/public/media');
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days)->getTimestamp();

        if (!is_dir($cacheDir)) {
            $this->info('Cache directory does not exist. Nothing to clean.');
            return self::SUCCESS;
        }

        $removed = 0;
        $checked = 0;
        $errors = 0;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($cacheDir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $checked++;

            // Check age
            if ($file->getMTime() < $cutoff) {
                if (@unlink($file->getPathname())) {
                    $removed++;
                } else {
                    $errors++;
                }
                continue;
            }

            // Check if source still exists
            // Cache key format: cache/{hash}-{w}x{h}-{mode}.webp
            $basename = $file->getFilename();
            if (preg_match('/^([a-f0-9]{64})-\d+x\d+-\w+\.webp$/', $basename, $matches)) {
                $hash = $matches[1];

                // Search for any source file with this content hash
                $sourceExists = $this->findSourceByHash($mediaDir, $hash);

                if (!$sourceExists) {
                    if (@unlink($file->getPathname())) {
                        $removed++;
                    } else {
                        $errors++;
                    }
                }
            }
        }

        // Clean up empty directories
        $this->removeEmptyDirectories($cacheDir);

        Log::channel('audit')->info('Image cache cleaned', [
            'checked' => $checked,
            'removed' => $removed,
            'errors' => $errors,
            'days_threshold' => $days,
        ]);

        $this->info("Checked: {$checked}, Removed: {$removed}, Errors: {$errors}");
        return self::SUCCESS;
    }

    /**
     * Check if any source file in the media directory has the given content hash.
     */
    private function findSourceByHash(string $dir, string $hash): bool
    {
        if (!is_dir($dir)) {
            return false;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }
            // Skip cache directory
            if (str_contains($file->getPathname(), '/cache/')) {
                continue;
            }
            if (hash_file('sha256', $file->getPathname()) === $hash) {
                return true;
            }
        }

        return false;
    }

    /**
     * Remove empty directories recursively.
     */
    private function removeEmptyDirectories(string $dir): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir() && count(scandir($item->getPathname())) <= 2) {
                @rmdir($item->getPathname());
            }
        }
    }
}
