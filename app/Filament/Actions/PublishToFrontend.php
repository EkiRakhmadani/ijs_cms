<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Artisan;

/**
 * Writes the current CMS content into the frontend as static JSON.
 *
 * The site prerenders every page at build time and never calls this API at
 * runtime, so editing here changes nothing on its own — publishing is what
 * moves content across, and a frontend rebuild is what puts it live.
 */
class PublishToFrontend
{
    public static function make(): Action
    {
        return Action::make('publishToFrontend')
            ->label('Publish ke frontend')
            ->icon(Heroicon::OutlinedCloudArrowUp)
            ->requiresConfirmation()
            ->modalHeading('Publish konten ke frontend?')
            ->modalDescription('Menulis JSON ke folder frontend. Perubahan baru terlihat di situs setelah frontend di-build ulang.')
            ->modalSubmitActionLabel('Publish')
            ->action(function (): void {
                $status = Artisan::call('content:export');
                $output = trim(Artisan::output());

                if ($status !== self::SUCCESS) {
                    Notification::make()
                        ->danger()
                        ->title('Publish gagal')
                        ->body($output !== '' ? $output : 'Perintah content:export keluar dengan status error.')
                        ->persistent()
                        ->send();

                    return;
                }

                Notification::make()
                    ->success()
                    ->title('Konten dipublikasikan')
                    ->body('Jalankan `npm run build` di frontend untuk menayangkannya.')
                    ->send();
            });
    }

    private const SUCCESS = 0;
}
