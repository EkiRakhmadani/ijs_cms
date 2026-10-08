<?php

namespace App\Filament\Resources\UiStrings\Pages;

use App\Filament\Resources\UiStrings\UiStringResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUiString extends EditRecord
{
    protected static string $resource = UiStringResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
