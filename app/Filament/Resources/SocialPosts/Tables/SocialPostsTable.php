<?php

namespace App\Filament\Resources\SocialPosts\Tables;

use App\Models\SocialPost;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SocialPostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->reorderable('position')
            ->columns([
                ImageColumn::make('mediaAsset.path')
                    ->label('')
                    ->disk('public')
                    ->height(48)
                    ->default(null),

                TextColumn::make('url')
                    ->label('Postingan')
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('kind')
                    ->label('Jenis panel')
                    ->state(fn (SocialPost $record): string => $record->media_asset_id === null
                        ? 'Embed Instagram'
                        : 'Foto')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Foto' ? 'success' : 'gray'),

                TextColumn::make('position')
                    ->label('Urutan')
                    ->alignEnd()
                    // Only the first three panels are rendered.
                    ->color(fn (SocialPost $record): string => $record->position > 3 ? 'gray' : 'primary'),
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
