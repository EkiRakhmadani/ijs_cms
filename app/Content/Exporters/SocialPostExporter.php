<?php

namespace App\Content\Exporters;

use App\Content\Contracts\Exporter;
use App\Models\SocialPost;

/**
 * The panels in Home's Social Media section.
 *
 * A post with a picture becomes that picture; one without becomes Instagram's
 * own embed of the permalink. Which of the two a panel is gets decided here,
 * in the data, exactly as the frontend's socialFeed module already expects.
 *
 * `image` is published as a plain path string because the markup renders it
 * with `<img src={post.image}>`. `caption` is an { id, en } pair: it is read
 * out to screen-reader users, and every visitor-facing string on this site is
 * bilingual.
 */
class SocialPostExporter implements Exporter
{
    public function filename(): string
    {
        return 'social-posts.json';
    }

    /**
     * @return array<array-key, mixed>
     */
    public function data(): array
    {
        return SocialPost::query()
            ->ordered()
            ->with('mediaAsset')
            ->get()
            ->map(function (SocialPost $post): array {
                $entry = ['url' => $post->url];

                if ($post->mediaAsset !== null) {
                    $entry['image'] = $post->mediaAsset->publishedUrl();
                    $entry['width'] = $post->mediaAsset->width;
                    $entry['height'] = $post->mediaAsset->height;
                }

                // Left out entirely when unwritten, so the panel falls back to
                // the generic label the markup already has for that case.
                if ($post->caption_id !== null || $post->caption_en !== null) {
                    $entry['caption'] = [
                        'id' => $post->caption_id,
                        'en' => $post->caption_en,
                    ];
                }

                return $entry;
            })
            ->all();
    }
}
