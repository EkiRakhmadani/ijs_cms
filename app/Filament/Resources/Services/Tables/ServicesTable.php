<?php

namespace App\Filament\Resources\Services\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // Same order the site lists them in, and draggable to change it.
            ->defaultSort('position')
            ->reorderable('position')
            ->columns([
                TextColumn::make('title')
                    ->label('Layanan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('Halaman')
                    ->formatStateUsing(fn (string $state): string => '/'.$state)
                    ->color('gray')
                    ->searchable(),

                TextColumn::make('items_count')
                    ->label('Item')
                    ->counts('items')
                    ->alignEnd(),

                TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
