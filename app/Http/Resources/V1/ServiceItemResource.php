<?php

namespace App\Http\Resources\V1;

use App\Models\ServiceItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ServiceItem
 */
class ServiceItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'desc' => [
                'id' => $this->desc_id,
                'en' => $this->desc_en,
            ],
        ];
    }
}
