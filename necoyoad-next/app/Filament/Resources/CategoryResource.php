<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * CategoryResource — Filament 3 declarative admin CRUD for Categories.
 *
 * Extends NecoyoadResource to inherit: store-scope bypass, audit hooks,
 * and the shared Descriptions/Stores/SEO tabs.
 */
class CategoryResource extends NecoyoadResource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationGroup = 'Catalog';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Category')
                ->tabs(array_merge(
                    [
                        Forms\Components\Tabs\Tab::make('General')->schema([
                            Forms\Components\Select::make('parent_id')
                                ->relationship('parent', 'id')
                                ->label('Parent Category')
                                ->nullable(),
                            Forms\Components\TextInput::make('image'),
                            Forms\Components\Toggle::make('status')->default(true),
                            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                        ])->columns(2),
                    ],
                    static::sharedTabs(),
                ))
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('parent_id')->label('Parent')->sortable(),
            Tables\Columns\IconColumn::make('status')->boolean(),
            Tables\Columns\TextColumn::make('sort_order')->sortable(),
        ])->filters([
            Tables\Filters\TernaryFilter::make('status'),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([
            Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
