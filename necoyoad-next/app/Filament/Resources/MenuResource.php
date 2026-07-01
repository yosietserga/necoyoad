<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\Menu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * MenuResource — admin CRUD for menus.
 * Menu links are managed via a Repeater (tree via parent_id).
 */
class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;
    protected static ?string $navigationIcon = 'heroicon-o-bars-3';
    protected static ?string $navigationGroup = 'CMS';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Menu')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('General')->schema([
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\Select::make('store_id')
                            ->relationship('store', 'name')->required(),
                        Forms\Components\TextInput::make('position')
                            ->placeholder('header, footer, sidebar'),
                        Forms\Components\Toggle::make('is_default')->default(false),
                        Forms\Components\Toggle::make('status')->default(true),
                        Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                    ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Links')->schema([
                        Forms\Components\Repeater::make('links')
                            ->relationship('links')
                            ->schema([
                                Forms\Components\TextInput::make('tag')->label('Label')->required(),
                                Forms\Components\TextInput::make('link')->label('URL')->required(),
                                Forms\Components\Select::make('parent_id')
                                    ->label('Parent Link')
                                    ->options(\App\Models\MenuLink::pluck('tag', 'id'))
                                    ->nullable(),
                                Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                            ])
                            ->columns(2)
                            ->orderable('sort_order'),
                    ]),
                ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('position'),
            Tables\Columns\TextColumn::make('store.name'),
            Tables\Columns\IconColumn::make('status')->boolean(),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
