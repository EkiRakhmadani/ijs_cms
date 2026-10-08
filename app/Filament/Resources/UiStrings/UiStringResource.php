<?php

namespace App\Filament\Resources\UiStrings;

use App\Filament\Resources\UiStrings\Pages\CreateUiString;
use App\Filament\Resources\UiStrings\Pages\EditUiString;
use App\Filament\Resources\UiStrings\Pages\ListUiStrings;
use App\Filament\Resources\UiStrings\Schemas\UiStringForm;
use App\Filament\Resources\UiStrings\Tables\UiStringsTable;
use App\Models\UiString;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UiStringResource extends Resource
{
    protected static ?string $model = UiString::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLanguage;

    protected static ?string $navigationLabel = 'Teks UI';

    protected static ?string $modelLabel = 'teks UI';

    public static function form(Schema $schema): Schema
    {
        return UiStringForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UiStringsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUiStrings::route('/'),
            'create' => CreateUiString::route('/create'),
            'edit' => EditUiString::route('/{record}/edit'),
        ];
    }
}
