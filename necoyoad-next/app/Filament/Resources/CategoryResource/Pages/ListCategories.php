<?php

declare(strict_types=1);

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;

class ListCategories extends ListRecords { protected static string $resource = CategoryResource::class; }
class CreateCategory extends CreateRecord { protected static string $resource = CategoryResource::class; }
class EditCategory extends EditRecord { protected static string $resource = CategoryResource::class; }
