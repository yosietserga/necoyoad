<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyResource\Pages;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * CurrencyResource — admin CRUD for system currencies.
 *
 * Extends NecoyoadResource to inherit: audit hooks (store-scope bypass
 * is a no-op since Currency does not use HasStoreAssignment).
 *
 * Currency `value` is the exchange rate relative to the system base
 * currency. The storefront formats prices with `symbol_left`/`symbol_right`
 * and `decimal_place`, and converts amounts using `value`.
 */
class CurrencyResource extends NecoyoadResource
{
    protected static ?string $model = Currency::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Currency')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('General')->schema([
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(8)
                            ->helperText('ISO 4217 code (e.g., "USD").'),
                        Forms\Components\TextInput::make('symbol_left')
                            ->maxLength(12)
                            ->helperText('Symbol placed before the amount (e.g., "$").'),
                        Forms\Components\TextInput::make('symbol_right')
                            ->maxLength(12)
                            ->helperText('Symbol placed after the amount (e.g., "€" for Euro formatted "1.234,56 €").'),
                        Forms\Components\TextInput::make('decimal_place')
                            ->numeric()
                            ->default(2)
                            ->minValue(0)
                            ->maxValue(4),
                        Forms\Components\TextInput::make('value')
                            ->numeric()
                            ->step(0.000001)
                            ->default(1)
                            ->helperText('Exchange rate relative to the system base currency (base = 1.0).'),
                        Forms\Components\Toggle::make('status')
                            ->default(true),
                    ])->columns(2),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->badge()->searchable()->sortable(),
                Tables\Columns\TextColumn::make('symbol_left')->toggleable(),
                Tables\Columns\TextColumn::make('symbol_right')->toggleable(),
                Tables\Columns\TextColumn::make('decimal_place')->sortable(),
                Tables\Columns\TextColumn::make('value')->numeric(decimalPlaces: 6)->sortable(),
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
            'index' => Pages\ListCurrencies::route('/'),
            'create' => Pages\CreateCurrency::route('/create'),
            'edit' => Pages\EditCurrency::route('/{record}/edit'),
        ];
    }
}
