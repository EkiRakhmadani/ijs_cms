<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Services\Pages\EditService;
use App\Filament\Resources\Services\Pages\ListServices;
use App\Filament\Resources\Services\RelationManagers\ItemsRelationManager;
use App\Models\Service;
use App\Models\ServiceItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;
use Tests\TestCase;

class ServicePanelTest extends TestCase
{
    use RefreshDatabase;

    private string $frontend;

    protected function setUp(): void
    {
        parent::setUp();

        $this->frontend = storage_path('framework/testing/frontend-panel-'.getmypid());
        File::ensureDirectoryExists($this->frontend.'/src/data');
        File::put($this->frontend.'/next.config.mjs', '');
        config()->set('content.frontend_path', $this->frontend);

        $this->actingAs(User::factory()->create());
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->frontend);

        parent::tearDown();
    }

    public function test_the_panel_requires_signing_in(): void
    {
        auth()->logout();

        $this->get('/admin/services')->assertRedirect('/admin/login');
    }

    public function test_it_lists_services_in_position_order(): void
    {
        $second = Service::factory()->create(['title' => 'Legal', 'position' => 2]);
        $first = Service::factory()->create(['title' => 'Finance', 'position' => 1]);

        Livewire::test(ListServices::class)
            ->assertCanSeeTableRecords([$first, $second], inOrder: true);
    }

    public function test_it_saves_an_edited_description(): void
    {
        $service = Service::factory()->create(['description_id' => 'Naskah lama.']);

        Livewire::test(EditService::class, ['record' => $service->getKey()])
            ->fillForm(['description_id' => 'Naskah baru.'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Naskah baru.', $service->refresh()->description_id);
    }

    public function test_it_rejects_a_slug_with_a_leading_slash(): void
    {
        $service = Service::factory()->create();

        Livewire::test(EditService::class, ['record' => $service->getKey()])
            ->fillForm(['slug' => '/services-legal'])
            ->call('save')
            ->assertHasFormErrors(['slug']);
    }

    public function test_it_rejects_a_duplicate_slug(): void
    {
        Service::factory()->create(['slug' => 'services-legal']);
        $other = Service::factory()->create(['slug' => 'services-it']);

        Livewire::test(EditService::class, ['record' => $other->getKey()])
            ->fillForm(['slug' => 'services-legal'])
            ->call('save')
            ->assertHasFormErrors(['slug']);
    }

    public function test_the_items_relation_manager_lists_items_in_order(): void
    {
        $service = Service::factory()->create();
        $second = ServiceItem::factory()->for($service)->create(['position' => 2]);
        $first = ServiceItem::factory()->for($service)->create(['position' => 1]);

        Livewire::test(ItemsRelationManager::class, [
            'ownerRecord' => $service,
            'pageClass' => EditService::class,
        ])->assertCanSeeTableRecords([$first, $second], inOrder: true);
    }

    public function test_publishing_writes_the_generated_file(): void
    {
        $service = Service::factory()->create(['slug' => 'services-legal']);
        ServiceItem::factory()->for($service)->create();

        $generated = $this->frontend.'/src/data/generated/services.json';
        $this->assertFileDoesNotExist($generated);

        Livewire::test(ListServices::class)
            ->callAction('publishToFrontend');

        $this->assertFileExists($generated);
        $this->assertSame(
            'services-legal',
            json_decode(file_get_contents($generated), true)[0]['slug'],
        );
    }
}
