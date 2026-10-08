<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ServiceItem>
 */
class ServiceItemFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'title' => Str::title(fake()->words(3, true)),
            'desc_id' => fake()->sentence(),
            'desc_en' => fake()->sentence(),
            'position' => 0,
        ];
    }
}
