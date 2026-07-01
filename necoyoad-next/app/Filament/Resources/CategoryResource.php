<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationGroup = 'Catalog';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Category')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('General')->schema([
                        Forms\Components\Select::make('parent_id')
                            ->relationship('parent', 'id')
                            ->label('Parent Category')
                            ->nullable(),
                        Forms\Components\TextInput::make('image'),
                        Forms\Components\Toggle::make('status')->default(true),
                        Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                    ])->columns(2),
                    Forms\Components\Tabs\Tab::make('Descriptions')->schema([
                        Forms\Components\Repeater::make('descriptions')
                            ->relationship('descriptions')
                            ->schema([
                                Forms\Components\Select::make('language_id')
                                    ->relationship('language', 'name')->required(),
                                Forms\Components\TextInput::make('title')->required(),
                                Forms\Components\Textarea::make('description')->rows(5),
                                Forms\Components\TextInput::make('seo_title'),
                                Forms\Components\Textarea::make('meta_description'),
                            ])->columns(2),
                    ]),
                    Forms\Components\Tabs\Tab::make('Stores')->schema([
                        Forms\Components\Select::make('stores')
                            ->relationship('stores', 'name')->multiple(),
                    ]),
                ])->columnSpanFull(),
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
