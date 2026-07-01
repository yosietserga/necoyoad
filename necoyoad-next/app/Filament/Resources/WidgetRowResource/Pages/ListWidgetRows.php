<?php

declare(strict_types=1);

namespace App\Filament\Resources\WidgetRowResource\Pages;

use App\Filament\Resources\WidgetRowResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;

class ListWidgetRows extends ListRecords { protected static string $resource = WidgetRowResource::class; }
class CreateWidgetRow extends CreateRecord { protected static string $resource = WidgetRowResource::class; }
class EditWidgetRow extends EditRecord { protected static string $resource = WidgetRowResource::class; }
