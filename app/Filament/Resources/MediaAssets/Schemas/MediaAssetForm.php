<?php

namespace App\Filament\Resources\MediaAssets\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MediaAssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Berkas')
                    ->description('Diunggah ke server ini, lalu disalin ke frontend saat Publish. Nama berkas di frontend dibuat dari isi berkasnya, sehingga mengganti gambar tidak pernah menyisakan versi lama di cache pengunjung.')
                    ->schema([
                        FileUpload::make('path')
                            ->label('Gambar')
                            ->image()
                            ->disk('public')
                            ->directory('media')
                            ->required()
                            ->maxSize(8 * 1024)
                            ->imagePreviewHeight('200')
                            ->helperText('WebP lebih kecil dan itu format yang dipakai situs. Maksimal 8 MB.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Teks alternatif')
                    ->description('Dibacakan pembaca layar. Wajib dua bahasa seperti semua teks yang sampai ke pengunjung. Kosongkan hanya kalau gambar murni dekoratif.')
                    ->columns(2)
                    ->schema([
                        Textarea::make('alt_id')
                            ->label('Indonesia')
                            ->rows(3),

                        Textarea::make('alt_en')
                            ->label('English')
                            ->rows(3),
                    ]),
            ]);
    }
}
