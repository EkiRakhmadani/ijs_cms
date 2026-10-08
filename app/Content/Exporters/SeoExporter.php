<?php

namespace App\Content\Exporters;

use App\Content\Contracts\Exporter;
use App\Models\PageSeo;
use App\Models\SiteSetting;

/**
 * What search engines and link previews see.
 *
 * Page descriptions are not published here when they are derived: the
 * frontend lifts the opening sentence of copy that already exists on the
 * page, so the description cannot drift from what the page actually says.
 * This publishes `descriptionKey` and lets it do that, falling back to an
 * authored description only where a page's copy supplies none.
 */
class SeoExporter implements Exporter
{
    public function filename(): string
    {
        return 'seo.json';
    }

    /**
     * @return array<array-key, mixed>
     */
    public function data(): array
    {
        $site = SiteSetting::current();

        $pages = [];

        foreach (PageSeo::query()->ordered()->get() as $page) {
            $entry = [
                'path' => $page->path,
                'title' => $page->title(),
            ];

            if ($page->description_key !== null) {
                $entry['descriptionKey'] = $page->description_key;
            } else {
                $entry['description'] = [
                    'id' => $page->description_id,
                    'en' => $page->description_en,
                ];
            }

            $pages[$page->key] = $entry;
        }

        return [
            'site' => [
                'url' => $site->url,
                'title' => $site->title,
                'name' => $site->name,
                'legalName' => $site->legal_name,
                'shortName' => $site->short_name,
                'locale' => ['id' => $site->locale_id, 'en' => $site->locale_en],
            ],
            'pages' => $pages,
        ];
    }
}
