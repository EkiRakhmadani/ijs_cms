<?php

namespace Database\Factories;

use App\Models\UiString;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UiString>
 */
class UiStringFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Dotted, prefixed by the surface it appears on — the group is
            // derived from that prefix.
            'key' => fake()->unique()->randomElement(['nav', 'footer', 'connect', 'error']).'.'.fake()->unique()->lexify('????'),
            'value_id' => fake()->words(3, true),
            'value_en' => fake()->words(3, true),
            'position' => 0,
        ];
    }
}
