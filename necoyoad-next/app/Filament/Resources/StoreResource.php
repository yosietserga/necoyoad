<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\StoreResource\Pages;
use App\Models\Store;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * StoreResource — admin CRUD for stores (multi-store management).
 *
 * Extends NecoyoadResource to inherit: audit hooks (store-scope bypass
 * is a no-op here since Store itself is the scope root).
 *
 * The settings JSON column stores per-store configuration (template,
 * language, currency, etc.).
 *
 * Note: sharedTabs() is NOT used because a Store is itself the scope
 * root — it does not have a descriptions/stores/SEO polymorphic relation.
 */
class StoreResource extends NecoyoadResource
{
    protected static ?string $model = Store::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Store')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('General')->schema([
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\TextInput::make('folder')->required()->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('domain'),
                        Forms\Components\Toggle::make('is_default'),
                        Forms\Components\Toggle::make('status')->default(true),
                    ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Languages')->schema([
                        Forms\Components\Select::make('languages')
                            ->relationship('languages', 'name')
                            ->multiple(),
                    ]),

                    Forms\Components\Tabs\Tab::make('Settings')->schema([
                        Forms\Components\KeyValue::make('settings')
                            ->label('Store Settings (JSON)')
                            ->keyLabel('Key')
                            ->valueLabel('Value'),
                    ]),
                ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('folder'),
            Tables\Columns\TextColumn::make('domain'),
            Tables\Columns\IconColumn::make('is_default')->boolean(),
            Tables\Columns\IconColumn::make('status')->boolean(),
        ])->actions([
            Tables\Actions\EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStores::route('/'),
            'create' => Pages\CreateStore::route('/create'),
            'edit' => Pages\EditStore::route('/{record}/edit'),
        ];
    }
}
