<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;

/**
 * NecoyoadResource — abstract base class for all Filament resources.
 *
 * Provides shared functionality mandated by the architecture:
 *   - Descriptions tab (multi-language name/description/SEO fields)
 *   - Store assignment tab (multi-store visibility)
 *   - SEO tab (meta_title, meta_description, meta_keywords via EAV)
 *   - Audit logging on create/update/delete (writes to user_activity)
 *   - Store-aware query scoping (admin sees all stores, not just current)
 *
 * Subclasses declare $model and implement form() + table(), calling
 * sharedTabs() to include the standard tabs.
 *
 * @see v6 (AdminController declarative CRUD)
 * @see v12 (NecoyoadResource base class)
 */
abstract class NecoyoadResource extends Resource
{
    /**
     * Get the standard shared tabs (Descriptions + Stores + SEO).
     * Subclasses call this in their form() method and merge with entity-specific tabs.
     */
    protected static function sharedTabs(): array
    {
        return [
            Forms\Components\Tabs\Tab::make('Descriptions')
                ->schema([
                    Forms\Components\Repeater::make('descriptions')
                        ->relationship('descriptions')
                        ->schema([
                            Forms\Components\Select::make('language_id')
                                ->relationship('language', 'name')
                                ->required()
                                ->label('Language'),
                            Forms\Components\TextInput::make('title')
                                ->required()
                                ->label('Title'),
                            Forms\Components\Textarea::make('description')
                                ->rows(5)
                                ->label('Description'),
                            Forms\Components\TextInput::make('seo_title')
                                ->label('SEO Title')
                                ->maxLength(60),
                            Forms\Components\Textarea::make('meta_description')
                                ->rows(2)
                                ->maxLength(160)
                                ->label('Meta Description'),
                            Forms\Components\TextInput::make('meta_keywords')
                                ->label('Meta Keywords'),
                        ])
                        ->columns(2)
                        ->collapsible()
                        ->label(''),
                ]),

            Forms\Components\Tabs\Tab::make('Stores')
                ->schema([
                    Forms\Components\Select::make('stores')
                        ->relationship('stores', 'name')
                        ->multiple()
                        ->preload()
                        ->label('Visible in Stores')
                        ->helperText('Select which stores this item is visible in. Leave empty for all stores.'),
                ]),

            Forms\Components\Tabs\Tab::make('SEO')
                ->schema([
                    Forms\Components\TextInput::make('seo_url_keyword')
                        ->label('SEO URL (slug)')
                        ->helperText('The URL keyword for this entity (e.g., "smartphone-pro"). Leave empty to auto-generate from title.'),
                ]),
        ];
    }

    /**
     * Override getEloquentQuery to exclude the store global scope in admin.
     * Admins should see all entities across all stores, not just the current one.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScope('store');
    }

    // Note: Audit logging is handled by the Auditable trait on each model
    // (via model boot events), NOT by Filament Resource hooks. This ensures
    // audit logging works for ALL write paths: Filament admin, API, tinker, seeders.
    // See app/Traits/Auditable.php
}
