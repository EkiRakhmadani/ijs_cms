<?php

namespace Database\Factories;

use App\Models\MediaAsset;
use App\Models\SocialPost;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SocialPost>
 */
class SocialPostFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // A shape-correct permalink that belongs to no real account —
            // this repository is published, so no live handle goes in it.
            'url' => 'https://www.instagram.com/p/'.Str::random(11).'/',
            'media_asset_id' => null,
            'caption_id' => null,
            'caption_en' => null,
            'position' => 0,
        ];
    }

    public function withImage(): static
    {
        return $this->state(fn (): array => [
            'media_asset_id' => MediaAsset::factory(),
        ]);
    }

    public function captioned(): static
    {
        return $this->state(fn (): array => [
            'caption_id' => fake()->sentence(),
            'caption_en' => fake()->sentence(),
        ]);
    }
}
