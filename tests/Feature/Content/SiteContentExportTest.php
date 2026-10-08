<?php

namespace Tests\Feature\Content;

use App\Content\Exporters\ContactExporter;
use App\Content\Exporters\SeoExporter;
use App\Content\Exporters\UiStringExporter;
use App\Models\ContactChannel;
use App\Models\PageSeo;
use App\Models\SiteSetting;
use App\Models\UiString;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteContentExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_label_that_reads_the_same_either_way_exports_as_one_string(): void
    {
        ContactChannel::factory()->create([
            'key' => 'phone',
            'href' => 'tel:+622157973088',
            'label_id' => '+6221-5797-3088',
            'label_en' => null,
        ]);

        // Written once rather than twice: that is what stops the two languages
        // drifting apart on text that was never meant to differ.
        $this->assertSame([
            'phone' => ['href' => 'tel:+622157973088', 'label' => '+6221-5797-3088'],
        ], (new ContactExporter)->data());
    }

    public function test_a_label_that_genuinely_differs_exports_as_a_pair(): void
    {
        ContactChannel::factory()->create([
            'key' => 'address',
            'href' => 'https://maps.app.goo.gl/x',
            'label_id' => 'Gedung Artha Graha Lt. 27',
            'label_en' => 'Artha Graha Building, 27th Floor',
        ]);

        $this->assertSame(
            ['id' => 'Gedung Artha Graha Lt. 27', 'en' => 'Artha Graha Building, 27th Floor'],
            (new ContactExporter)->data()['address']['label'],
        );
    }

    public function test_an_identical_english_label_still_collapses_to_one_string(): void
    {
        ContactChannel::factory()->create(['key' => 'email', 'label_id' => 'a@b.test', 'label_en' => 'a@b.test']);

        $this->assertSame('a@b.test', (new ContactExporter)->data()['email']['label']);
    }

    public function test_postal_fields_are_published_only_where_they_exist(): void
    {
        ContactChannel::factory()->postal()->create(['key' => 'address', 'position' => 1]);
        ContactChannel::factory()->create(['key' => 'phone', 'position' => 2]);

        $data = (new ContactExporter)->data();

        $this->assertArrayHasKey('postal', $data['address']);
        $this->assertArrayNotHasKey('postal', $data['phone']);
    }

    public function test_strings_export_as_two_tables_in_authoring_order(): void
    {
        UiString::factory()->create(['key' => 'nav.home', 'value_id' => 'Beranda', 'value_en' => 'Home', 'position' => 1]);
        UiString::factory()->create(['key' => 'lang.switch', 'value_id' => 'Bahasa', 'value_en' => 'Language', 'position' => 0]);

        $data = (new UiStringExporter)->data();

        $this->assertSame(['lang.switch', 'nav.home'], array_keys($data['id']));
        $this->assertSame(['lang.switch', 'nav.home'], array_keys($data['en']));
        $this->assertSame('Beranda', $data['id']['nav.home']);
        $this->assertSame('Home', $data['en']['nav.home']);
    }

    public function test_a_derived_description_publishes_its_key_not_the_sentence(): void
    {
        PageSeo::factory()->create(['key' => 'home', 'path' => '/', 'description_key' => 'about.p1']);

        $page = (new SeoExporter)->data()['pages']['home'];

        // The frontend lifts the opening sentence itself, so the description
        // cannot drift from the copy it was taken from.
        $this->assertSame('about.p1', $page['descriptionKey']);
        $this->assertArrayNotHasKey('description', $page);
    }

    public function test_an_authored_description_publishes_as_text(): void
    {
        PageSeo::factory()->authoredDescription()->create([
            'key' => 'about',
            'description_id' => 'Ditulis sendiri.',
            'description_en' => 'Written by hand.',
        ]);

        $page = (new SeoExporter)->data()['pages']['about'];

        $this->assertSame(['id' => 'Ditulis sendiri.', 'en' => 'Written by hand.'], $page['description']);
        $this->assertArrayNotHasKey('descriptionKey', $page);
    }

    public function test_a_page_with_no_title_publishes_null_for_the_bare_site_title(): void
    {
        PageSeo::factory()->create(['key' => 'home', 'title_id' => null, 'title_en' => null]);

        $this->assertNull((new SeoExporter)->data()['pages']['home']['title']);
    }

    public function test_the_site_identity_exports_in_the_frontends_shape(): void
    {
        SiteSetting::factory()->create(['url' => 'https://ptijs.test', 'short_name' => 'IJS']);

        $site = (new SeoExporter)->data()['site'];

        $this->assertSame('https://ptijs.test', $site['url']);
        $this->assertSame('IJS', $site['shortName']);
        $this->assertSame(['id' => 'id_ID', 'en' => 'en_US'], $site['locale']);
    }
}
