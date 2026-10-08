<?php

namespace App\Filament\Resources\SiteSettings\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SiteSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Judul situs'),
                TextColumn::make('url')->label('URL'),
                TextColumn::make('legal_name')->label('Nama legal')->color('gray'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
