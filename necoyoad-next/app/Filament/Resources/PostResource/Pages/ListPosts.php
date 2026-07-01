<?php

declare(strict_types=1);

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;

class ListPosts extends ListRecords { protected static string $resource = PostResource::class; }
class CreatePost extends CreateRecord { protected static string $resource = PostResource::class; }
class EditPost extends EditRecord { protected static string $resource = PostResource::class; }
