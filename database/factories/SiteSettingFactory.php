<?php

namespace Database\Factories;

use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SiteSetting>
 */
class SiteSettingFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url' => 'https://ptijs.test',
            'title' => 'IJS - Internusa Jayaabadi Sentosa',
            'name' => 'Internusa Jayaabadi Sentosa',
            'legal_name' => 'PT Internusa Jayaabadi Sentosa',
            'short_name' => 'IJS',
            'locale_id' => 'id_ID',
            'locale_en' => 'en_US',
        ];
    }
}
