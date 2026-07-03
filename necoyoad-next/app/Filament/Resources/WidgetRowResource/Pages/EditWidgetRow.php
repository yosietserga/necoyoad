<?php

declare(strict_types=1);

namespace App\Filament\Resources\WidgetRowResource\Pages;

use App\Filament\Resources\WidgetRowResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWidgetRow extends EditRecord
{
    protected static string $resource = WidgetRowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
