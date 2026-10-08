<?php

namespace App\Filament\Resources\ContactChannels\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactChannelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kanal')
                    ->columns(2)
                    ->schema([
                        TextInput::make('key')
                            ->label('Kunci')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->rule('regex:/^[a-z][a-z0-9_]*$/')
                            ->helperText('Dipakai kode untuk memanggil kanal ini (phone, email, address). Mengubahnya memutus tempat yang memakainya.'),

                        TextInput::make('position')
                            ->label('Urutan')
                            ->required()
                            ->numeric()
                            ->default(0),

                        TextInput::make('href')
                            ->label('Tautan')
                            ->required()
                            ->maxLength(255)
                            ->helperText('tel:+622157973088 · mailto:… · https://… — sengaja dipisah dari label, karena nomor yang didial dan yang dibaca bukan string yang sama.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Label yang terbaca')
                    ->description('Isi sisi Inggris hanya kalau teksnya memang berbeda. Dikosongkan, satu teks dipakai untuk kedua bahasa — itu yang menjaga keduanya tidak melenceng pada teks yang tidak pernah dimaksudkan berbeda.')
                    ->columns(2)
                    ->schema([
                        Textarea::make('label_id')
                            ->label('Indonesia')
                            ->required()
                            ->rows(3),

                        Textarea::make('label_en')
                            ->label('English (opsional)')
                            ->rows(3),
                    ]),

                Section::make('Alamat terstruktur')
                    ->description('Hanya untuk kanal alamat. Dibaca mesin pencari sebagai schema.org PostalAddress, bukan untuk ditampilkan. Pindah kantor berarti mengubah ini dan labelnya sekaligus.')
                    ->collapsed()
                    ->schema([
                        KeyValue::make('postal')
                            ->label('')
                            ->keyLabel('Field')
                            ->valueLabel('Isi')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
