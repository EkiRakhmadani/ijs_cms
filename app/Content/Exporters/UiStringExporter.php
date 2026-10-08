<?php

namespace App\Content\Exporters;

use App\Content\Contracts\Exporter;
use App\Models\UiString;

/**
 * UI copy, as the two flat tables the frontend's `t()` reads from.
 *
 * Published in authoring order, so the file stays grouped by the surface each
 * string belongs to — and so the diff only moves when the copy does.
 */
class UiStringExporter implements Exporter
{
    public function filename(): string
    {
        return 'strings.json';
    }

    /**
     * @return array<array-key, mixed>
     */
    public function data(): array
    {
        $strings = ['en' => [], 'id' => []];

        foreach (UiString::query()->ordered()->get() as $string) {
            $strings['en'][$string->key] = $string->value_en;
            $strings['id'][$string->key] = $string->value_id;
        }

        return $strings;
    }
}
