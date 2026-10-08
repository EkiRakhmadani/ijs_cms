<?php

namespace Database\Factories;

use App\Models\MediaAsset;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends Factory<MediaAsset>
 */
class MediaAssetFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'path' => 'media/'.Str::random(16).'.png',
            'original_name' => fake()->slug(2).'.png',
            'mime' => 'image/png',
            'width' => 1080,
            'height' => 1080,
            'bytes' => 2048,
            'sha256' => hash('sha256', Str::random(32)),
            'alt_id' => fake()->sentence(),
            'alt_en' => fake()->sentence(),
        ];
    }

    /**
     * Put a real (tiny) PNG behind the record, for anything that has to copy
     * or measure the file rather than just read its row.
     */
    public function onDisk(): static
    {
        return $this->afterMaking(function ($asset): void {
            Storage::disk('public')->put($asset->path, self::onePixelPng());
        });
    }

    public static function onePixelPng(): string
    {
        return base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
        );
    }
}
