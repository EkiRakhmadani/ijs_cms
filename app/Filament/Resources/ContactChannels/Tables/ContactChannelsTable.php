<?php

namespace App\Filament\Resources\ContactChannels\Tables;

use App\Models\ContactChannel;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContactChannelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->reorderable('position')
            ->columns([
                TextColumn::make('key')
                    ->label('Kanal')
                    ->badge()
                    ->searchable(),

                TextColumn::make('label_id')
                    ->label('Label')
                    ->limit(60)
                    ->searchable(),

                TextColumn::make('label_en')
                    ->label('Dua bahasa?')
                    ->state(fn (ContactChannel $record): string => $record->label_en === null ? 'Satu teks' : 'Ya')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Ya' ? 'success' : 'gray'),

                TextColumn::make('href')
                    ->label('Tautan')
                    ->limit(40)
                    ->color('gray'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
