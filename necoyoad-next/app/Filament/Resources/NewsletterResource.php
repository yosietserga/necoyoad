<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\NewsletterResource\Pages;
use App\Models\Newsletter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * NewsletterResource — admin CRUD for reusable newsletter bodies.
 *
 * Extends NecoyoadResource to inherit: audit hooks (store-scope bypass
 * is a no-op since Newsletter does not use HasStoreAssignment).
 *
 * A Newsletter holds the text + HTML body that a Campaign dispatches.
 * One Newsletter can be sent by many Campaigns.
 *
 * @see v4 (campaign pipeline)
 */
class NewsletterResource extends NecoyoadResource
{
    protected static ?string $model = Newsletter::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Marketing';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Newsletter')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('General')->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Internal label for this newsletter body.'),
                        Forms\Components\Toggle::make('status')
                            ->label('Active')
                            ->default(true),
                    ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Text Body')->schema([
                        Forms\Components\Textarea::make('textbody')
                            ->rows(12)
                            ->label('Plain-Text Body')
                            ->helperText('Plain-text version of the newsletter. Sent to clients that do not accept HTML.'),
                    ]),

                    Forms\Components\Tabs\Tab::make('HTML Body')->schema([
                        Forms\Components\RichEditor::make('htmlbody')
                            ->label('HTML Body')
                            ->helperText('Full HTML body of the newsletter. Merge tags supported by the campaign pipeline may be used.'),
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
                Tables\Columns\TextColumn::make('campaigns_count')
                    ->counts('campaigns')
                    ->label('Campaigns')
                    ->sortable(),
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
            'index' => Pages\ListNewsletters::route('/'),
            'create' => Pages\CreateNewsletter::route('/create'),
            'edit' => Pages\EditNewsletter::route('/{record}/edit'),
        ];
    }
}
