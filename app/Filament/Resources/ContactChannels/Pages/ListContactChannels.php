<?php

namespace App\Filament\Resources\ContactChannels\Pages;

use App\Filament\Actions\PublishToFrontend;
use App\Filament\Resources\ContactChannels\ContactChannelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContactChannels extends ListRecords
{
    protected static string $resource = ContactChannelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PublishToFrontend::make(),
            CreateAction::make(),
        ];
    }
}
