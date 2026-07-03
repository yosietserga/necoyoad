<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\WidgetRowResource\Pages;
use App\Models\WidgetRow;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * WidgetRowResource — the widget layout manager.
 *
 * Extends NecoyoadResource to inherit: audit hooks (store-scope bypass
 * is a no-op here since WidgetRow uses a direct store_id BelongsTo).
 *
 * This is the admin interface for the visual widget tree editor.
 * It manages widget_rows -> widget_columns -> widgets (the layout tree).
 *
 * In a full implementation, this would be a drag-and-drop Livewire page
 * (see Livewire\WidgetEditor\WidgetTree). This Filament resource provides
 * the CRUD fallback for direct row/column/widget management.
 *
 * Note: sharedTabs() is NOT used because WidgetRow does not have the
 * polymorphic descriptions/stores/SEO relations — it uses a direct
 * store_id foreign key.
 *
 * @see v3 (style/widget.php — widget layout manager)
 * @see v6 (visual editors — style/ folder)
 * @see v11 (widget engine preservation — visual editor hooks)
 */
class WidgetRowResource extends NecoyoadResource
{
    protected static ?string $model = WidgetRow::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Design';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Widget Row')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('Row Settings')->schema([
                        Forms\Components\Select::make('store_id')
                            ->relationship('store', 'name')->required(),
                        Forms\Components\Select::make('position')
                            ->options([
                                'featuredContent' => 'Featured Content',
                                'main' => 'Main Content',
                                'featuredFooter' => 'Featured Footer',
                                'column_left' => 'Left Column',
                                'column_right' => 'Right Column',
                                'header' => 'Header',
                                'footer' => 'Footer',
                            ])->required(),
                        Forms\Components\TextInput::make('key')->required(),
                        Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                        Forms\Components\Toggle::make('status')->default(true),
                        Forms\Components\KeyValue::make('settings')
                            ->label('Row Settings (classnames, sticky, layout_width)'),
                    ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Columns')->schema([
                        Forms\Components\Repeater::make('columns')
                            ->relationship('columns')
                            ->schema([
                                Forms\Components\TextInput::make('key')->required(),
                                Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                                Forms\Components\KeyValue::make('settings')
                                    ->label('Column Settings (grid_large, grid_medium, grid_small, classnames)'),
                            ])
                            ->columns(2)
                            ->orderable('sort_order'),
                    ]),

                    Forms\Components\Tabs\Tab::make('Widgets')->schema([
                        Forms\Components\Placeholder::make('widgets_hint')
                            ->content('Widgets are managed within columns. Edit a column to add widgets.'),
                    ]),
                ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('store.name')->label('Store'),
            Tables\Columns\TextColumn::make('position')->badge(),
            Tables\Columns\TextColumn::make('key'),
            Tables\Columns\TextColumn::make('sort_order')->sortable(),
            Tables\Columns\IconColumn::make('status')->boolean(),
        ])->filters([
            Tables\Filters\SelectFilter::make('store')
                ->relationship('store', 'name'),
            Tables\Filters\SelectFilter::make('position')
                ->options([
                    'featuredContent' => 'Featured Content',
                    'main' => 'Main Content',
                    'featuredFooter' => 'Featured Footer',
                    'column_left' => 'Left Column',
                    'column_right' => 'Right Column',
                ]),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWidgetRows::route('/'),
            'create' => Pages\CreateWidgetRow::route('/create'),
            'edit' => Pages\EditWidgetRow::route('/{record}/edit'),
        ];
    }
}
