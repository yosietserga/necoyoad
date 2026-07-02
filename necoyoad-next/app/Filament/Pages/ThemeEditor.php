<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Page;

/**
 * ThemeEditor — Filament admin page for the code editor.
 *
 * Renders a split-pane layout: file tree sidebar + Monaco editor. The actual
 * file operations are performed via the REST API at /admin/api/theme/* which
 * delegates to ThemeEditorService.
 *
 * Access: users with 'theme-edit' ability (via filament-shield or Gate).
 */
class ThemeEditor extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';
    protected static ?string $navigationGroup = 'Design';
    protected static ?string $navigationLabel = 'Theme Editor';
    protected static ?int $navigationSort = 100;

    protected static string $view = 'filament.pages.theme-editor';

    protected static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('theme-edit') ?? true;
    }

    public function getViewData(): array
    {
        return [
            'activeTheme' => app('store.context')?->setting('config_template', 'choroni') ?? 'choroni',
        ];
    }
}
