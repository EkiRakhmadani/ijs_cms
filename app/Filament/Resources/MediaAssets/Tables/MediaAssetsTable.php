<?php

namespace App\Filament\Resources\MediaAssets\Tables;

use App\Models\MediaAsset;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MediaAssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('path')
                    ->label('')
                    ->disk('public')
                    ->height(56),

                TextColumn::make('original_name')
                    ->label('Berkas')
                    ->searchable()
                    ->description(fn (MediaAsset $record): string => $record->publishedUrl()),

                TextColumn::make('width')
                    ->label('Ukuran')
                    ->formatStateUsing(fn (?int $state, MediaAsset $record): string => $state === null
                        ? '—'
                        : "{$state} × {$record->height}")
                    ->alignEnd(),

                TextColumn::make('bytes')
                    ->label('Besar')
                    ->formatStateUsing(fn (?int $state): string => $state === null
                        ? '—'
                        : number_format($state / 1024, 0).' KB')
                    ->alignEnd(),

                TextColumn::make('social_posts_count')
                    ->label('Dipakai')
                    ->counts('socialPosts')
                    ->alignEnd()
                    // An asset nothing points at is published but unused, and
                    // `content:export --prune` is what clears it out.
                    ->color(fn (int $state): string => $state === 0 ? 'gray' : 'success'),
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
