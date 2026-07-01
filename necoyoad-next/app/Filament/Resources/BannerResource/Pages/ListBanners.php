<?php

declare(strict_types=1);

namespace App\Filament\Resources\BannerResource\Pages;

use App\Filament\Resources\BannerResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;

class ListBanners extends ListRecords { protected static string $resource = BannerResource::class; }
class CreateBanner extends CreateRecord { protected static string $resource = BannerResource::class; }
class EditBanner extends EditRecord { protected static string $resource = BannerResource::class; }
