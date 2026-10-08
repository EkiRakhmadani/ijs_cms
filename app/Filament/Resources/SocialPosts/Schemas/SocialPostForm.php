<?php

namespace App\Filament\Resources\SocialPosts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SocialPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Postingan')
                    ->description('Dengan gambar, panel menjadi foto itu dan mengklik membuka postingannya. Tanpa gambar, panel menjadi embed Instagram sendiri — lebih otomatis, tapi tampil sebagai kartu putih yang tidak bisa ditata ulang dan bisa diblokir privacy blocker.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('url')
                            ->label('Tautan postingan')
                            ->required()
                            ->url()
                            ->maxLength(255)
                            // Posts, reels and IGTV share one embed endpoint.
                            ->rule('regex:#instagram\.com/(p|reel|tv)/[A-Za-z0-9_-]+#')
                            ->helperText('Contoh: https://www.instagram.com/p/XXXXXXXXXXX/')
                            ->columnSpanFull(),

                        Select::make('media_asset_id')
                            ->label('Gambar')
                            ->relationship('mediaAsset', 'original_name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Tanpa gambar — pakai embed Instagram')
                            ->helperText('Unggah dulu di menu Media.'),

                        TextInput::make('position')
                            ->label('Urutan')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Hanya tiga panel pertama yang tampil.'),
                    ]),

                Section::make('Keterangan untuk pembaca layar')
                    ->description('Hanya berpengaruh pada panel bergambar — di sana panel adalah foto tanpa teks apa pun di dalamnya. Dikosongkan, panel memperkenalkan dirinya dengan label umum.')
                    ->columns(2)
                    ->schema([
                        Textarea::make('caption_id')
                            ->label('Indonesia')
                            ->rows(3),

                        Textarea::make('caption_en')
                            ->label('English')
                            ->rows(3),
                    ]),
            ]);
    }
}
