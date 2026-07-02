<?php

declare(strict_types=1);

namespace App\Filament\Resources\WidgetRowResource\Pages;

use App\Filament\Resources\WidgetRowResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWidgetRow extends CreateRecord
{
    protected static string $resource = WidgetRowResource::class;
}
