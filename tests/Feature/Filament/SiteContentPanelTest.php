<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\ContactChannels\Pages\ListContactChannels;
use App\Filament\Resources\PageSeos\Pages\EditPageSeo;
use App\Filament\Resources\PageSeos\Pages\ListPageSeos;
use App\Filament\Resources\SiteSettings\Pages\ListSiteSettings;
use App\Filament\Resources\SiteSettings\SiteSettingResource;
use App\Filament\Resources\UiStrings\Pages\EditUiString;
use App\Filament\Resources\UiStrings\Pages\ListUiStrings;
use App\Models\ContactChannel;
use App\Models\PageSeo;
use App\Models\SiteSetting;
use App\Models\UiString;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;
use Tests\TestCase;

class SiteContentPanelTest extends TestCase
{
    use RefreshDatabase;

    private string $frontend;

    protected function setUp(): void
    {
        parent::setUp();

        $this->frontend = storage_path('framework/testing/frontend-sitecontent-'.getmypid());
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

    public function test_the_contact_list_renders(): void
    {
        $channel = ContactChannel::factory()->create();

        Livewire::test(ListContactChannels::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$channel]);
    }

    public function test_the_ui_string_list_renders_grouped(): void
    {
        $string = UiString::factory()->create(['key' => 'nav.home']);

        Livewire::test(ListUiStrings::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$string]);
    }

    public function test_saving_a_string_derives_its_group_from_the_key(): void
    {
        $string = UiString::factory()->create(['key' => 'nav.home']);

        Livewire::test(EditUiString::class, ['record' => $string->getKey()])
            ->fillForm(['key' => 'footer.rights'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('footer', $string->refresh()->group);
    }

    public function test_it_rejects_a_key_without_a_surface_prefix(): void
    {
        $string = UiString::factory()->create();

        Livewire::test(EditUiString::class, ['record' => $string->getKey()])
            ->fillForm(['key' => 'nodots'])
            ->call('save')
            ->assertHasFormErrors(['key']);
    }

    public function test_a_page_needs_a_description_when_none_is_derived(): void
    {
        $page = PageSeo::factory()->create(['description_key' => 'about.p1']);

        Livewire::test(EditPageSeo::class, ['record' => $page->getKey()])
            ->fillForm(['description_key' => null, 'description_id' => null, 'description_en' => null])
            ->call('save')
            ->assertHasFormErrors(['description_id', 'description_en']);
    }

    public function test_the_page_seo_and_site_lists_render(): void
    {
        $page = PageSeo::factory()->create();
        SiteSetting::current();

        Livewire::test(ListPageSeos::class)->assertSuccessful()->assertCanSeeTableRecords([$page]);
        Livewire::test(ListSiteSettings::class)->assertSuccessful();
    }

    public function test_the_site_identity_cannot_be_duplicated_or_deleted(): void
    {
        // One site, one identity — the panel should not offer a second.
        $this->assertFalse(SiteSettingResource::canCreate());
        $this->assertFalse(
            SiteSettingResource::canDelete(SiteSetting::current())
        );
    }

    public function test_publishing_writes_every_content_file(): void
    {
        ContactChannel::factory()->create(['key' => 'phone']);
        UiString::factory()->create(['key' => 'nav.home']);
        PageSeo::factory()->create(['key' => 'home']);

        Livewire::test(ListContactChannels::class)->callAction('publishToFrontend');

        $dir = $this->frontend.'/src/data/generated/';

        foreach (['services.json', 'social-posts.json', 'contact.json', 'strings.json', 'seo.json'] as $file) {
            $this->assertFileExists($dir.$file);
        }
    }
}
