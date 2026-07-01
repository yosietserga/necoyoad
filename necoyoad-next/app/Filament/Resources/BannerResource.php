<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * BannerResource — admin CRUD for banners.
 *
 * The jquery_plugin field is the discriminator (v9) that drives
 * the storefront's dynamic template + asset selection.
 */
class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'CMS';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Banner')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('General')->schema([
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\Select::make('jquery_plugin')
                            ->options([
                                'nivo-slider' => 'NivoSlider',
                                'slick' => 'Slick Carousel',
                                'camera' => 'Camera Slideshow',
                                'fancybox-gallery' => 'Fancybox Gallery',
                                'grid-gallery' => 'CSS Grid Gallery',
                            ])
                            ->default('nivo-slider')
                            ->required(),
                        Forms\Components\DatePicker::make('publish_date_start'),
                        Forms\Components\DatePicker::make('publish_date_end'),
                        Forms\Components\Toggle::make('status')->default(true),
                    ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Slides')->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Forms\Components\TextInput::make('image')->required(),
                                Forms\Components\TextInput::make('link'),
                                Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                                Forms\Components\Toggle::make('status')->default(true),
                            ])
                            ->columns(2)
                            ->orderable('sort_order'),
                    ]),

                    Forms\Components\Tabs\Tab::make('Slide Descriptions')->schema([
                        Forms\Components\Placeholder::make('desc_hint')
                            ->content('Each slide can have per-language descriptions (title, text) via the Descriptions polymorphic table.'),
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
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('jquery_plugin')->badge(),
            Tables\Columns\IconColumn::make('status')->boolean(),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
