<?php

namespace App\Content\Exporters;

use App\Content\Contracts\Exporter;
use App\Models\ContactChannel;

/**
 * IJS's contact channels, keyed exactly as the frontend's CONTACT object is.
 *
 * `href` and `label` stay apart because they are not the same string: the
 * number dials as +622157973088 and reads as +6221-5797-3088.
 */
class ContactExporter implements Exporter
{
    public function filename(): string
    {
        return 'contact.json';
    }

    /**
     * @return array<array-key, mixed>
     */
    public function data(): array
    {
        $channels = [];

        foreach (ContactChannel::query()->ordered()->get() as $channel) {
            $entry = [
                'href' => $channel->href,
                'label' => $channel->label(),
            ];

            if ($channel->postal !== null) {
                $entry['postal'] = $channel->postal;
            }

            $channels[$channel->key] = $entry;
        }

        return $channels;
    }
}
