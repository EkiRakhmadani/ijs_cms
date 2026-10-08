<?php

namespace App\Filament\Resources\PageSeos;

use App\Filament\Resources\PageSeos\Pages\CreatePageSeo;
use App\Filament\Resources\PageSeos\Pages\EditPageSeo;
use App\Filament\Resources\PageSeos\Pages\ListPageSeos;
use App\Filament\Resources\PageSeos\Schemas\PageSeoForm;
use App\Filament\Resources\PageSeos\Tables\PageSeosTable;
use App\Models\PageSeo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PageSeoResource extends Resource
{
    protected static ?string $model = PageSeo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    protected static ?string $navigationLabel = 'SEO Halaman';

    protected static ?string $modelLabel = 'halaman';

    public static function form(Schema $schema): Schema
    {
        return PageSeoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PageSeosTable::configure($table);
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
            'index' => ListPageSeos::route('/'),
            'create' => CreatePageSeo::route('/create'),
            'edit' => EditPageSeo::route('/{record}/edit'),
        ];
    }
}
