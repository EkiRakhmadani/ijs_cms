<?php

namespace App\Filament\Resources\UiStrings\Tables;

use App\Models\UiString;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UiStringsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->reorderable('position')
            // 67 strings across a dozen surfaces: grouping is what makes the
            // list something you can find a string in rather than scroll.
            ->defaultGroup('group')
            ->columns([
                TextColumn::make('key')
                    ->label('Kunci')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('value_id')
                    ->label('Indonesia')
                    ->limit(55)
                    ->searchable()
                    ->wrap(),

                TextColumn::make('value_en')
                    ->label('English')
                    ->limit(55)
                    ->searchable()
                    ->wrap(),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->label('Permukaan')
                    ->options(fn (): array => UiString::query()
                        ->distinct()
                        ->orderBy('group')
                        ->pluck('group', 'group')
                        ->all()),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
