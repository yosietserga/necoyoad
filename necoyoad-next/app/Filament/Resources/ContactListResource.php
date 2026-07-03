<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ContactListResource\Pages;
use App\Models\ContactList;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * ContactListResource — admin CRUD for marketing mailing lists (segments).
 *
 * Extends NecoyoadResource to inherit: audit hooks (store-scope bypass
 * is a no-op since ContactList does not use HasStoreAssignment).
 *
 * Lists group Contacts via the contact_list_subscriptions pivot table.
 * Campaigns select recipients via these lists.
 *
 * @see v4 (campaign pipeline)
 */
class ContactListResource extends NecoyoadResource
{
    protected static ?string $model = ContactList::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';
    protected static ?string $navigationGroup = 'Marketing';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Contact List')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('General')->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('status')
                            ->label('Active')
                            ->default(true),
                    ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Description')->schema([
                        Forms\Components\Textarea::make('description')
                            ->rows(4)
                            ->maxLength(1000),
                    ]),

                    Forms\Components\Tabs\Tab::make('Contacts')->schema([
                        Forms\Components\Select::make('contacts')
                            ->relationship('contacts', 'email')
                            ->multiple()
                            ->preload()
                            ->label('Subscribed Contacts')
                            ->helperText('Contacts that belong to this list. They will receive campaigns dispatched to this list.'),
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
                Tables\Columns\TextColumn::make('description')->limit(50)->toggleable(),
                Tables\Columns\TextColumn::make('contacts_count')
                    ->counts('contacts')
                    ->label('Contacts')
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
            'index' => Pages\ListContactLists::route('/'),
            'create' => Pages\CreateContactList::route('/create'),
            'edit' => Pages\EditContactList::route('/{record}/edit'),
        ];
    }
}
