<?php

namespace App\Http\Resources\V1;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Service
 *
 * Shaped to match what the Next.js frontend already consumes in
 * src/data/servicesData.js: fields that read the same in both languages stay
 * plain strings, and only the copy that genuinely differs is an { id, en } pair.
 */
class ServiceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'path' => $this->path(),
            'title' => $this->title,
            'icon' => $this->icon,
            'description' => [
                'id' => $this->description_id,
                'en' => $this->description_en,
            ],
            'items' => ServiceItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
