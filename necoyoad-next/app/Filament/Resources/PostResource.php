<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * PostResource — admin CRUD for blog posts AND static pages.
 *
 * Posts and pages share the same `posts` table with a `type` discriminator
 * (v8: 'post' vs 'page'). This resource handles both via a filter.
 */
class PostResource extends Resource
{
    protected static ?string $model = Post::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'CMS';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Post')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('General')->schema([
                        Forms\Components\Select::make('type')
                            ->options(['post' => 'Blog Post', 'page' => 'Static Page'])
                            ->default('post')
                            ->required(),
                        Forms\Components\TextInput::make('image'),
                        Forms\Components\TextInput::make('template')
                            ->placeholder('Leave empty for default template'),
                        Forms\Components\Toggle::make('publish')->default(true),
                        Forms\Components\Toggle::make('allow_reviews')->default(false),
                        Forms\Components\Toggle::make('status')->default(true),
                        Forms\Components\DateTimePicker::make('date_publish_start'),
                        Forms\Components\DateTimePicker::make('date_publish_end'),
                        Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                    ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Descriptions')->schema([
                        Forms\Components\Repeater::make('descriptions')
                            ->relationship('descriptions')
                            ->schema([
                                Forms\Components\Select::make('language_id')
                                    ->relationship('language', 'name')->required(),
                                Forms\Components\TextInput::make('title')->required(),
                                Forms\Components\RichEditor::make('description')->columnSpanFull(),
                                Forms\Components\TextInput::make('seo_title'),
                                Forms\Components\Textarea::make('meta_description'),
                                Forms\Components\TextInput::make('meta_keywords'),
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
            Tables\Columns\TextColumn::make('type')->badge()
                ->colors(['post' => 'info', 'page' => 'success']),
            Tables\Columns\IconColumn::make('status')->boolean(),
            Tables\Columns\IconColumn::make('publish')->boolean(),
            Tables\Columns\TextColumn::make('date_publish_start')->dateTime()->sortable(),
        ])->filters([
            Tables\Filters\SelectFilter::make('type')
                ->options(['post' => 'Blog Posts', 'page' => 'Pages']),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
