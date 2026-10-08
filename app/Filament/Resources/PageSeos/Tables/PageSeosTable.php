<?php

namespace App\Filament\Resources\PageSeos\Tables;

use App\Models\PageSeo;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PageSeosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->reorderable('position')
            ->columns([
                TextColumn::make('path')->label('Path')->searchable(),

                TextColumn::make('title_id')
                    ->label('Judul')
                    ->placeholder('judul situs apa adanya')
                    ->searchable(),

                TextColumn::make('description_key')
                    ->label('Deskripsi')
                    ->state(fn (PageSeo $record): string => $record->description_key !== null
                        ? "diturunkan dari {$record->description_key}"
                        : 'ditulis sendiri')
                    ->badge()
                    ->color(fn (string $state): string => str_starts_with($state, 'diturunkan') ? 'success' : 'warning'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
