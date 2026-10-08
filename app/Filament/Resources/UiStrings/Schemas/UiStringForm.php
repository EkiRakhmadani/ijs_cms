<?php

namespace App\Filament\Resources\UiStrings\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UiStringForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kunci')
                    ->description('Kode memanggil teks ini lewat kuncinya. Menambah kunci baru di sini tidak membuatnya muncul di mana pun — komponen harus memakainya. Sebaliknya, menghapus kunci yang dipakai komponen membuat kuncinya sendiri yang tampil di layar.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('key')
                            ->label('Kunci')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->rule('regex:/^[a-z][a-zA-Z0-9]*(\.[a-zA-Z0-9]+)+$/')
                            ->helperText('Bertitik, diawali nama permukaannya: nav.home, footer.rights.'),

                        TextInput::make('position')
                            ->label('Urutan')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Menentukan urutan di berkas hasil publish.'),
                    ]),

                Section::make('Teks')
                    ->description('Keduanya wajib. Ini teks yang dibaca pengunjung.')
                    ->columns(2)
                    ->schema([
                        Textarea::make('value_id')
                            ->label('Indonesia')
                            ->required()
                            ->rows(4),

                        Textarea::make('value_en')
                            ->label('English')
                            ->required()
                            ->rows(4),
                    ]),
            ]);
    }
}
