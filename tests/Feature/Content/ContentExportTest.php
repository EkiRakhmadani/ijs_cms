<?php

namespace Tests\Feature\Content;

use App\Content\ContentWriter;
use App\Content\Exporters\ServiceExporter;
use App\Models\Service;
use App\Models\ServiceItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Tests\TestCase;

class ContentExportTest extends TestCase
{
    use RefreshDatabase;

    private string $frontend;

    protected function setUp(): void
    {
        parent::setUp();

        // A stand-in frontend, so tests never write into the real checkout.
        $this->frontend = storage_path('framework/testing/frontend-'.getmypid());
        File::ensureDirectoryExists($this->frontend.'/src/data');
        File::put($this->frontend.'/next.config.mjs', '');

        config()->set('content.frontend_path', $this->frontend);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->frontend);

        parent::tearDown();
    }

    private function generated(string $file = 'services.json'): string
    {
        return $this->frontend.'/src/data/generated/'.$file;
    }

    public function test_it_shapes_services_the_way_the_frontend_reads_them(): void
    {
        $service = Service::factory()->create([
            'slug' => 'services-legal',
            'title' => 'Legal',
            'icon' => '§',
            'description_id' => 'Dukungan hukum menyeluruh.',
            'description_en' => 'End-to-end legal support.',
        ]);
        ServiceItem::factory()->for($service)->create([
            'title' => 'Corporate Legal Support',
            'desc_id' => 'Perizinan usaha.',
            'desc_en' => 'Business licensing.',
        ]);

        $data = (new ServiceExporter)->data();

        $this->assertSame([[
            'slug' => 'services-legal',
            'title' => 'Legal',
            'icon' => '§',
            'description' => [
                'id' => 'Dukungan hukum menyeluruh.',
                'en' => 'End-to-end legal support.',
            ],
            'items' => [[
                'title' => 'Corporate Legal Support',
                'desc' => [
                    'id' => 'Perizinan usaha.',
                    'en' => 'Business licensing.',
                ],
            ]],
        ]], $data);
    }

    public function test_services_and_items_export_in_position_order(): void
    {
        $second = Service::factory()->create(['slug' => 'services-second', 'position' => 2]);
        $first = Service::factory()->create(['slug' => 'services-first', 'position' => 1]);

        ServiceItem::factory()->for($first)->create(['title' => 'Kedua', 'position' => 2]);
        ServiceItem::factory()->for($first)->create(['title' => 'Pertama', 'position' => 1]);
        ServiceItem::factory()->for($second)->create();

        $data = (new ServiceExporter)->data();

        $this->assertSame(['services-first', 'services-second'], array_column($data, 'slug'));
        $this->assertSame(['Pertama', 'Kedua'], array_column($data[0]['items'], 'title'));
    }

    public function test_it_writes_the_generated_file(): void
    {
        Service::factory()->create(['slug' => 'services-legal']);

        $this->artisan('content:export')->assertSuccessful();

        $this->assertFileExists($this->generated());
        $this->assertSame(
            'services-legal',
            json_decode(file_get_contents($this->generated()), true)[0]['slug'],
        );
    }

    public function test_a_dry_run_writes_nothing(): void
    {
        Service::factory()->create();

        $this->artisan('content:export', ['--dry-run' => true])->assertSuccessful();

        $this->assertFileDoesNotExist($this->generated());
    }

    public function test_re_running_with_no_changes_leaves_the_file_untouched(): void
    {
        Service::factory()->create();

        $writer = ContentWriter::fromConfig();
        $data = (new ServiceExporter)->data();

        $this->assertTrue($writer->write('services.json', $data), 'first write should create the file');
        $this->assertFalse($writer->write('services.json', $data), 'identical data should not rewrite');
    }

    public function test_the_json_stays_readable_so_git_diffs_mean_something(): void
    {
        $service = Service::factory()->create(['slug' => 'services-legal']);
        ServiceItem::factory()->for($service)->create(['desc_id' => 'Soal “kutipan” dan /garis miring/.']);

        $this->artisan('content:export')->assertSuccessful();

        $raw = file_get_contents($this->generated());

        $this->assertStringContainsString("\n", $raw, 'pretty printed');
        $this->assertStringContainsString('“kutipan”', $raw, 'unicode is not escaped');
        $this->assertStringNotContainsString('\/', $raw, 'slashes are not escaped');
        $this->assertStringEndsWith("\n", $raw, 'trailing newline');
    }

    public function test_it_refuses_to_write_outside_the_frontend(): void
    {
        $decoy = storage_path('framework/testing/not-the-frontend-'.getmypid());
        File::ensureDirectoryExists($decoy);
        config()->set('content.frontend_path', $decoy);

        try {
            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage('does not look like the frontend');

            ContentWriter::fromConfig()->guard();
        } finally {
            File::deleteDirectory($decoy);
        }
    }

    public function test_the_command_fails_loudly_on_a_bad_frontend_path(): void
    {
        config()->set('content.frontend_path', '/tmp/definitely-not-here-'.getmypid());

        $this->artisan('content:export')->assertFailed();
    }
}
