<?php

namespace Database\Factories;

use App\Models\ContactChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactChannel>
 */
class ContactChannelFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => fake()->unique()->lexify('channel_???'),
            'href' => 'https://example.test/'.fake()->slug(1),
            // Most channels read the same either way and carry only one side.
            'label_id' => fake()->words(3, true),
            'label_en' => null,
            'postal' => null,
            'position' => 0,
        ];
    }

    public function bilingual(): static
    {
        return $this->state(fn (): array => ['label_en' => fake()->words(3, true)]);
    }

    public function postal(): static
    {
        return $this->state(fn (): array => [
            'postal' => [
                'streetAddress' => fake()->streetAddress(),
                'addressLocality' => 'Jakarta Selatan',
                'addressRegion' => 'DKI Jakarta',
                'postalCode' => '12190',
                'addressCountry' => 'ID',
            ],
        ]);
    }
}
