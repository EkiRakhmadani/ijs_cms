<?php

namespace App\Filament\Resources\Services\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Item layanan';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Nama item')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->helperText('Sama di kedua bahasa — ini nama produk, seperti nama layanannya.'),

                Textarea::make('desc_id')
                    ->label('Deskripsi (Indonesia)')
                    ->required()
                    ->rows(4),

                Textarea::make('desc_en')
                    ->label('Deskripsi (English)')
                    ->required()
                    ->rows(4),

                TextInput::make('position')
                    ->label('Urutan')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->defaultSort('position')
            ->reorderable('position')
            ->columns([
                TextColumn::make('title')
                    ->label('Item')
                    ->searchable(),

                TextColumn::make('desc_id')
                    ->label('Indonesia')
                    ->limit(60)
                    ->color('gray'),
            ])
            ->headerActions([
                // No AssociateAction: items belong to one service and are
                // created in place, never borrowed from another.
                CreateAction::make()->label('Tambah item'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
