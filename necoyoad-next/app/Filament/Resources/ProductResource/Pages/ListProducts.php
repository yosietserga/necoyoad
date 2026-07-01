<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;
}

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
}

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;
}
