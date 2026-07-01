<?php

declare(strict_types=1);

namespace App\Filament\Resources\StoreResource\Pages;

use App\Filament\Resources\StoreResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;

class ListStores extends ListRecords { protected static string $resource = StoreResource::class; }
class CreateStore extends CreateRecord { protected static string $resource = StoreResource::class; }
class EditStore extends EditRecord { protected static string $resource = StoreResource::class; }
