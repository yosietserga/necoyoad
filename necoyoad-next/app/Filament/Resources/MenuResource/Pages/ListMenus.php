<?php

declare(strict_types=1);

namespace App\Filament\Resources\MenuResource\Pages;

use App\Filament\Resources\MenuResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;

class ListMenus extends ListRecords { protected static string $resource = MenuResource::class; }
class CreateMenu extends CreateRecord { protected static string $resource = MenuResource::class; }
class EditMenu extends EditRecord { protected static string $resource = MenuResource::class; }
