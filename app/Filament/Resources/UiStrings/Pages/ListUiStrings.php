<?php

namespace App\Filament\Resources\UiStrings\Pages;

use App\Filament\Actions\PublishToFrontend;
use App\Filament\Resources\UiStrings\UiStringResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUiStrings extends ListRecords
{
    protected static string $resource = UiStringResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PublishToFrontend::make(),
            CreateAction::make(),
        ];
    }
}
