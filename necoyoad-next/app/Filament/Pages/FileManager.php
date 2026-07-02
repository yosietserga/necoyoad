<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Page;

/**
 * FileManager — Filament admin page for the file manager.
 *
 * Renders a full-page file browser with drag-drop upload, directory tree,
 * thumbnail grid, and context actions. The actual file operations are
 * performed via the REST API at /admin/api/filemanager/* which delegates
 * to FileManagerService.
 *
 * Access: users with 'file-manager' ability (via filament-shield or Gate).
 */
class FileManager extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'File Manager';
    protected static ?int $navigationSort = 99;

    protected static string $view = 'filament.pages.file-manager';

    protected static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('file-manager') ?? true;
    }

    public function getViewData(): array
    {
        return [
            'uploadUrl' => '/admin/api/filemanager/upload',
            'maxFileSize' => config('necoyoad.filemanager.max_file_size', 10485760),
            'allowedExtensions' => config('necoyoad.filemanager.allowed_extensions', []),
        ];
    }
}
