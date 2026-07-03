<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\CampaignResource\Pages;
use App\Models\Campaign;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * CampaignResource — admin CRUD for marketing email campaigns.
 *
 * Extends NecoyoadResource to inherit: audit hooks (store-scope bypass
 * is a no-op since Campaign does not use HasStoreAssignment).
 *
 * Campaigns dispatch a Newsletter to a recipient list over a date range,
 * with per-campaign sender identity and tracking toggles.
 *
 * @see v4 (campaign pipeline)
 */
class CampaignResource extends NecoyoadResource
{
    protected static ?string $model = Campaign::class;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Marketing';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Campaign')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('General')->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('newsletter_id')
                            ->relationship('newsletter', 'name')
                            ->required()
                            ->label('Newsletter'),
                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('status')
                            ->label('Active')
                            ->default(true),
                    ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Sender')->schema([
                        Forms\Components\TextInput::make('from_name')
                            ->required()
                            ->maxLength(255)
                            ->label('From Name'),
                        Forms\Components\TextInput::make('from_email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->label('From Email'),
                        Forms\Components\TextInput::make('replyto_email')
                            ->email()
                            ->maxLength(255)
                            ->label('Reply-To Email'),
                    ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Schedule')->schema([
                        Forms\Components\DateTimePicker::make('date_start')
                            ->label('Start Date'),
                        Forms\Components\DateTimePicker::make('date_end')
                            ->label('End Date'),
                        Forms\Components\Select::make('repeat')
                            ->options([
                                'none' => 'No repeat',
                                'daily' => 'Daily',
                                'weekly' => 'Weekly',
                                'monthly' => 'Monthly',
                            ])
                            ->default('none'),
                    ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Tracking')->schema([
                        Forms\Components\Toggle::make('trace_email')
                            ->label('Track Email Opens')
                            ->default(true),
                        Forms\Components\Toggle::make('trace_click')
                            ->label('Track Link Clicks')
                            ->default(true),
                        Forms\Components\Toggle::make('embed_image')
                            ->label('Embed Images (no external requests)')
                            ->default(false),
                    ])->columns(1),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('newsletter.name')->label('Newsletter')->sortable(),
                Tables\Columns\TextColumn::make('subject')->limit(50)->toggleable(),
                Tables\Columns\TextColumn::make('date_start')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('date_end')->dateTime()->sortable(),
                Tables\Columns\IconColumn::make('status')->boolean(),
                Tables\Columns\IconColumn::make('trace_email')->boolean()->toggleable(),
                Tables\Columns\IconColumn::make('trace_click')->boolean()->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('status'),
                Tables\Filters\SelectFilter::make('newsletter')
                    ->relationship('newsletter', 'name'),
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
            'index' => Pages\ListCampaigns::route('/'),
            'create' => Pages\CreateCampaign::route('/create'),
            'edit' => Pages\EditCampaign::route('/{record}/edit'),
        ];
    }
}
