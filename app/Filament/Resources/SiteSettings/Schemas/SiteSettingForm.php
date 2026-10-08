<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SiteSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Alamat')
                    ->schema([
                        TextInput::make('url')
                            ->label('URL kanonis')
                            ->required()
                            ->url()
                            ->helperText('Satu-satunya alamat situs ini dikenal. ptijs.com dan www.ptijs.com sama-sama mengarah ke sini, dan www harus 301-redirect ke alamat ini — kalau tidak, mesin pencari melihat dua salinan dari setiap halaman.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Nama')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')->label('Judul situs')->required()
                            ->helperText('Dipakai sebagai <title> halaman utama.'),
                        TextInput::make('name')->label('Nama')->required(),
                        TextInput::make('legal_name')->label('Nama legal')->required(),
                        TextInput::make('short_name')->label('Singkatan')->required(),
                    ]),

                Section::make('Locale')
                    ->columns(2)
                    ->schema([
                        TextInput::make('locale_id')->label('Indonesia')->required(),
                        TextInput::make('locale_en')->label('English')->required(),
                    ]),
            ]);
    }
}
