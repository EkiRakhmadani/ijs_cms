<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas')
                    ->description('Nama layanan dipakai apa adanya di kedua bahasa — ini nama produk, dan sudah ditulis dalam bahasa Inggris di seluruh copy Indonesia.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Nama layanan')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            // Tanpa garis miring di depan: frontend yang menambahkannya.
                            ->rule('regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                            ->helperText('Tanpa garis miring di depan. Halaman jadi /<slug>. Mengubahnya memutus tautan lama.'),

                        TextInput::make('position')
                            ->label('Urutan')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Menentukan urutan di navigasi dan sitemap. Bisa juga diatur dengan menyeret baris di daftar.'),

                        TextInput::make('icon')
                            ->label('Ikon (tidak terpakai)')
                            ->maxLength(255)
                            ->helperText('Peninggalan data lama. Ikon yang benar-benar digambar situs ada di serviceIcons.js, dipetakan per slug — jadi isian ini tidak mengubah apa pun.'),
                    ]),

                Section::make('Deskripsi')
                    ->description('Sisi Indonesia adalah naskah asli; sisi Inggris terjemahannya. Kalimat pertama dipakai ulang sebagai deskripsi SEO halaman ini.')
                    ->columns(2)
                    ->schema([
                        Textarea::make('description_id')
                            ->label('Indonesia')
                            ->required()
                            ->rows(8),

                        Textarea::make('description_en')
                            ->label('English')
                            ->required()
                            ->rows(8),
                    ]),
            ]);
    }
}
