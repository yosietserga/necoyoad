<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

/**
 * UserResource — admin CRUD for backoffice admin users.
 *
 * Extends NecoyoadResource to inherit: audit hooks (store-scope bypass
 * is a no-op since User does not use HasStoreAssignment).
 *
 * The password field is hashed on save via dehydrateStateUsing + the
 * dehydrated() gate, so existing passwords are preserved when the field
 * is left empty on edit.
 */
class UserResource extends NecoyoadResource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('User')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('General')->schema([
                        Forms\Components\TextInput::make('username')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(64),
                        Forms\Components\TextInput::make('firstname')
                            ->required()
                            ->maxLength(64),
                        Forms\Components\TextInput::make('lastname')
                            ->required()
                            ->maxLength(64),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\Toggle::make('status')
                            ->label('Active')
                            ->default(true),
                    ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Security')->schema([
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->maxLength(255)
                            ->helperText('Leave empty on edit to keep the existing password.'),
                    ])->columns(1),

                    Forms\Components\Tabs\Tab::make('Profile')->schema([
                        Forms\Components\TextInput::make('image')
                            ->label('Avatar Image Path')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('ip')
                            ->label('Last Known IP')
                            ->ip()
                            ->helperText('Usually populated automatically on login. Leave empty unless you need to override.'),
                    ])->columns(2),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('username')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('firstname')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('lastname')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\IconColumn::make('status')->boolean(),
                Tables\Columns\TextColumn::make('ip')->toggleable(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
