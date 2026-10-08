<?php

namespace App\Filament\Resources\PageSeos\Schemas;

use App\Models\UiString;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PageSeoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Halaman')
                    ->columns(2)
                    ->schema([
                        TextInput::make('key')
                            ->label('Kunci')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('home, about — dipakai kode untuk memanggil entri ini.'),

                        TextInput::make('path')
                            ->label('Path')
                            ->required()
                            ->helperText('Tanpa awalan bahasa. URL Inggrisnya diturunkan otomatis.'),

                        TextInput::make('position')
                            ->label('Urutan')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->columnSpanFull(),
                    ]),

                Section::make('Judul')
                    ->description('Dikosongkan keduanya, halaman memakai judul situs apa adanya tanpa nama halaman di depannya — itulah yang dipakai halaman utama.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title_id')->label('Indonesia'),
                        TextInput::make('title_en')->label('English'),
                    ]),

                Section::make('Deskripsi')
                    ->description('Lebih baik diturunkan daripada ditulis ulang: pilih teks UI yang sudah ada di halaman, dan kalimat pertamanya dipakai sebagai deskripsi. Dengan begitu deskripsi tidak bisa melenceng dari isi halaman. Teks tulisan tangan di bawahnya hanya dipakai kalau tidak ada teks sumber yang dipilih.')
                    ->schema([
                        Select::make('description_key')
                            ->label('Diturunkan dari teks UI')
                            ->options(fn (): array => UiString::query()
                                ->ordered()
                                ->pluck('key', 'key')
                                ->all())
                            ->searchable()
                            ->live()
                            ->placeholder('— tulis sendiri di bawah —')
                            ->columnSpanFull(),

                        Textarea::make('description_id')
                            ->label('Indonesia')
                            ->rows(3)
                            ->disabled(fn ($get): bool => filled($get('description_key')))
                            ->required(fn ($get): bool => blank($get('description_key'))),

                        Textarea::make('description_en')
                            ->label('English')
                            ->rows(3)
                            ->disabled(fn ($get): bool => filled($get('description_key')))
                            ->required(fn ($get): bool => blank($get('description_key'))),
                    ])
                    ->columns(2),
            ]);
    }
}
