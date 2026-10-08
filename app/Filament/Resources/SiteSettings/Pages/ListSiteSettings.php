<?php

namespace App\Filament\Resources\SiteSettings\Pages;

use App\Filament\Actions\PublishToFrontend;
use App\Filament\Resources\SiteSettings\SiteSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSiteSettings extends ListRecords
{
    protected static string $resource = SiteSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PublishToFrontend::make(),
            CreateAction::make(),
        ];
    }
}
