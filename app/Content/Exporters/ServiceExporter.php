<?php

namespace App\Content\Exporters;

use App\Content\Contracts\Exporter;
use App\Models\Service;

/**
 * The service catalogue, in the shape the frontend's servicesData module
 * already expects.
 *
 * Fields that read the same in either language stay plain strings — service
 * names are product names, written in English throughout the Indonesian copy —
 * and only the copy that genuinely differs becomes an { id, en } pair. That
 * mirrors what the frontend's `tx()` helper assumes, so the generated file is
 * a drop-in for the literal it replaces.
 *
 * Slugs are stored canonically here, without the leading slash the frontend
 * links with; the data module adds it back.
 */
class ServiceExporter implements Exporter
{
    public function filename(): string
    {
        return 'services.json';
    }

    /**
     * @return array<array-key, mixed>
     */
    public function data(): array
    {
        return Service::query()
            ->ordered()
            ->with('items')
            ->get()
            ->map(fn (Service $service): array => [
                'slug' => $service->slug,
                'title' => $service->title,
                'icon' => $service->icon,
                'description' => [
                    'id' => $service->description_id,
                    'en' => $service->description_en,
                ],
                'items' => $service->items
                    ->map(fn ($item): array => [
                        'title' => $item->title,
                        'desc' => [
                            'id' => $item->desc_id,
                            'en' => $item->desc_en,
                        ],
                    ])
                    ->all(),
            ])
            ->all();
    }
}
