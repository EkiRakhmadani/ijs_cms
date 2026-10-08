<?php

namespace Database\Factories;

use App\Models\PageSeo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PageSeo>
 */
class PageSeoFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => fake()->unique()->slug(1),
            'path' => '/'.fake()->unique()->slug(1),
            'title_id' => fake()->words(2, true),
            'title_en' => fake()->words(2, true),
            // Derived by default: that is the shape the site is built around.
            'description_key' => 'about.p1',
            'description_id' => null,
            'description_en' => null,
            'position' => 0,
        ];
    }

    /**
     * A page whose copy supplies no opening sentence to lift.
     */
    public function authoredDescription(): static
    {
        return $this->state(fn (): array => [
            'description_key' => null,
            'description_id' => fake()->sentence(),
            'description_en' => fake()->sentence(),
        ]);
    }
}
