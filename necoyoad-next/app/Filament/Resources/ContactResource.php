<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * ContactResource — admin CRUD for newsletter/mailing contacts (subscribers).
 *
 * Extends NecoyoadResource to inherit: audit hooks (store-scope bypass
 * is a no-op since Contact does not use HasStoreAssignment).
 *
 * Each contact has an unsubscribe_token used by the campaign pipeline's
 * List-Unsubscribe header and the /unsubscribe/{token} storefront route.
 *
 * @see v4 (campaign pipeline)
 */
class ContactResource extends NecoyoadResource
{
    protected static ?string $model = Contact::class;
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Marketing';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Contact')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('General')->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('telephone')
                            ->tel()
                            ->maxLength(64),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Subscribed')
                            ->default(true),
                    ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Subscription')->schema([
                        Forms\Components\TextInput::make('unsubscribe_token')
                            ->label('Unsubscribe Token')
                            ->helperText('Unique token used by the List-Unsubscribe header and the /unsubscribe/{token} route. Leave empty to auto-generate on create.'),
                            Forms\Components\DateTimePicker::make('date_deleted')
                            ->label('Soft-Deleted At'),
                    ])->columns(2),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('telephone')->toggleable(),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Subscribed'),
                Tables\Columns\TextColumn::make('date_deleted')->dateTime()->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Subscribed')
                    ->trueLabel('Subscribed')
                    ->falseLabel('Unsubscribed'),
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
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }
}
