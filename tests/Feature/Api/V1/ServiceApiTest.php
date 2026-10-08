<?php

namespace Tests\Feature\Api\V1;

use App\Models\Service;
use App\Models\ServiceItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_every_service_with_its_items(): void
    {
        $service = Service::factory()->create([
            'slug' => 'services-legal',
            'title' => 'Legal',
            'icon' => '§',
        ]);
        ServiceItem::factory()->for($service)->create(['title' => 'Corporate Legal Support']);

        $this->getJson('/api/v1/services')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [[
                    'slug', 'path', 'title', 'icon',
                    'description' => ['id', 'en'],
                    'items' => [['title', 'desc' => ['id', 'en']]],
                ]],
            ])
            ->assertJsonPath('data.0.slug', 'services-legal')
            ->assertJsonPath('data.0.path', '/services-legal')
            ->assertJsonPath('data.0.icon', '§')
            ->assertJsonPath('data.0.items.0.title', 'Corporate Legal Support');
    }

    public function test_index_orders_services_by_position(): void
    {
        Service::factory()->create(['slug' => 'services-second', 'position' => 2]);
        Service::factory()->create(['slug' => 'services-first', 'position' => 1]);

        $this->getJson('/api/v1/services')
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'services-first')
            ->assertJsonPath('data.1.slug', 'services-second');
    }

    public function test_show_resolves_a_service_by_its_slug(): void
    {
        $service = Service::factory()->create(['slug' => 'services-it']);
        ServiceItem::factory()->for($service)->create(['title' => 'Helpdesk Support']);

        $this->getJson('/api/v1/services/services-it')
            ->assertOk()
            ->assertJsonPath('data.slug', 'services-it')
            ->assertJsonPath('data.items.0.title', 'Helpdesk Support');
    }

    public function test_show_returns_not_found_for_an_unknown_slug(): void
    {
        $this->getJson('/api/v1/services/services-does-not-exist')
            ->assertNotFound();
    }

    public function test_items_come_back_in_position_order(): void
    {
        $service = Service::factory()->create(['slug' => 'services-hrga']);
        ServiceItem::factory()->for($service)->create(['title' => 'Kedua', 'position' => 2]);
        ServiceItem::factory()->for($service)->create(['title' => 'Pertama', 'position' => 1]);

        $this->getJson('/api/v1/services/services-hrga')
            ->assertOk()
            ->assertJsonPath('data.items.0.title', 'Pertama')
            ->assertJsonPath('data.items.1.title', 'Kedua');
    }

    public function test_both_languages_are_returned_so_the_frontend_can_switch(): void
    {
        $service = Service::factory()->create([
            'slug' => 'services-legal',
            'description_id' => 'Dukungan hukum menyeluruh.',
            'description_en' => 'End-to-end legal support.',
        ]);
        ServiceItem::factory()->for($service)->create([
            'desc_id' => 'Perizinan usaha.',
            'desc_en' => 'Business licensing.',
        ]);

        $this->getJson('/api/v1/services/services-legal')
            ->assertOk()
            ->assertJsonPath('data.description.id', 'Dukungan hukum menyeluruh.')
            ->assertJsonPath('data.description.en', 'End-to-end legal support.')
            ->assertJsonPath('data.items.0.desc.id', 'Perizinan usaha.')
            ->assertJsonPath('data.items.0.desc.en', 'Business licensing.');
    }
}
