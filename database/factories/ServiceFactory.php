<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'slug' => 'services-'.Str::slug($title),
            'title' => Str::title($title),
            'icon' => fake()->randomElement(['$', '§', '#', '@', '%']),
            'description_id' => fake()->paragraph(),
            'description_en' => fake()->paragraph(),
            'position' => 0,
        ];
    }
}
