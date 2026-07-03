<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * ProductResource — Filament 3 declarative admin CRUD for Products.
 *
 * Extends NecoyoadResource to inherit: store-scope bypass, audit hooks,
 * and the shared Descriptions/Stores/SEO tabs.
 *
 * Entity-specific tabs (General, Pricing, Categories) are declared here;
 * the shared tabs are pulled in via sharedTabs().
 *
 * @see v6 (AdminController declarative CRUD)
 * @see v12 (pattern 2: AdminController -> Filament Resources)
 */
class ProductResource extends NecoyoadResource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Catalog';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Product')
                ->tabs(array_merge(
                    [
                        // === General Tab ===
                        Forms\Components\Tabs\Tab::make('General')->schema([
                            Forms\Components\TextInput::make('sku')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->label('SKU'),
                            Forms\Components\TextInput::make('model')
                                ->label('Model'),
                            Forms\Components\TextInput::make('image')
                                ->label('Image Path'),
                            Forms\Components\Toggle::make('status')
                                ->default(true),
                            Forms\Components\Toggle::make('featured')
                                ->default(false),
                            Forms\Components\TextInput::make('sort_order')
                                ->numeric()
                                ->default(0),
                        ])->columns(2),

                        // === Pricing Tab ===
                        Forms\Components\Tabs\Tab::make('Pricing')->schema([
                            Forms\Components\TextInput::make('price')
                                ->numeric()
                                ->prefix('$')
                                ->required(),
                            Forms\Components\TextInput::make('cost')
                                ->numeric()
                                ->prefix('$'),
                            Forms\Components\TextInput::make('quantity')
                                ->numeric()
                                ->default(0),
                            Forms\Components\Toggle::make('subtract')
                                ->default(true)
                                ->label('Subtract Stock'),
                            Forms\Components\TextInput::make('minimum')
                                ->numeric()
                                ->default(1)
                                ->label('Minimum Quantity'),
                        ])->columns(2),

                        // === Categories Tab ===
                        Forms\Components\Tabs\Tab::make('Categories')->schema([
                            Forms\Components\Select::make('categories')
                                ->relationship('categories', 'id')
                                ->multiple()
                                ->label('Categories'),
                        ]),
                    ],
                    static::sharedTabs(),
                ))
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('model')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('price')->money('USD')->sortable(),
                Tables\Columns\TextColumn::make('quantity')->sortable(),
                Tables\Columns\IconColumn::make('status')->boolean(),
                Tables\Columns\IconColumn::make('featured')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('status'),
                Tables\Filters\TernaryFilter::make('featured'),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
