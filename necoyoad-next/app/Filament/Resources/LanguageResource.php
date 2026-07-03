<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\LanguageResource\Pages;
use App\Models\Language;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * LanguageResource — admin CRUD for system languages.
 *
 * Extends NecoyoadResource to inherit: audit hooks (store-scope bypass
 * is a no-op since Language does not use HasStoreAssignment).
 *
 * Languages are scoped to Stores via the store_languages pivot table
 * and feed the LanguageContext 6-level detection priority chain (v5).
 */
class LanguageResource extends NecoyoadResource
{
    protected static ?string $model = Language::class;
    protected static ?string $navigationIcon = 'heroicon-o-language';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Language')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('General')->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(64)
                            ->helperText('Display name (e.g., "English").'),
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(8)
                            ->helperText('ISO 639-1 code (e.g., "en").'),
                        Forms\Components\TextInput::make('locale')
                            ->required()
                            ->maxLength(20)
                            ->helperText('Locale identifier (e.g., "en_US").'),
                        Forms\Components\TextInput::make('directory')
                            ->maxLength(64)
                            ->helperText('Locale directory name on disk (e.g., "english").'),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('status')
                            ->default(true),
                    ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Stores')->schema([
                        Forms\Components\Select::make('stores')
                            ->relationship('stores', 'name')
                            ->multiple()
                            ->preload()
                            ->label('Available In Stores')
                            ->helperText('Stores that this language is enabled for. The storefront LanguageContext picks one based on the 6-level detection priority chain.'),
                    ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('code')->badge()->sortable(),
                Tables\Columns\TextColumn::make('locale')->toggleable(),
                Tables\Columns\TextColumn::make('directory')->toggleable(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
                Tables\Columns\IconColumn::make('status')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLanguages::route('/'),
            'create' => Pages\CreateLanguage::route('/create'),
            'edit' => Pages\EditLanguage::route('/{record}/edit'),
        ];
    }
}
