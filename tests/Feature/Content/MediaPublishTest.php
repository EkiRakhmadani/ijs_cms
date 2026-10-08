<?php

namespace Tests\Feature\Content;

use App\Content\Exporters\SocialPostExporter;
use App\Content\MediaPublisher;
use App\Models\MediaAsset;
use App\Models\SocialPost;
use Database\Factories\MediaAssetFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaPublishTest extends TestCase
{
    use RefreshDatabase;

    private string $frontend;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->frontend = storage_path('framework/testing/frontend-media-'.getmypid());
        File::ensureDirectoryExists($this->frontend.'/src/data');
        File::put($this->frontend.'/next.config.mjs', '');
        config()->set('content.frontend_path', $this->frontend);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->frontend);

        parent::tearDown();
    }

    private function storedAsset(string $path = 'media/post.png'): MediaAsset
    {
        Storage::disk('public')->put($path, MediaAssetFactory::onePixelPng());

        return MediaAsset::create(['path' => $path, 'original_name' => 'post.png']);
    }

    private function mediaDir(): string
    {
        return $this->frontend.'/public/media';
    }

    public function test_saving_an_asset_reads_its_facts_off_the_file(): void
    {
        $asset = $this->storedAsset();

        $this->assertSame(1, $asset->width);
        $this->assertSame(1, $asset->height);
        $this->assertSame('image/png', $asset->mime);
        $this->assertNotNull($asset->sha256);
        $this->assertGreaterThan(0, $asset->bytes);
    }

    public function test_the_published_name_is_the_content_hash(): void
    {
        $asset = $this->storedAsset();

        $this->assertSame(substr($asset->sha256, 0, 12).'.png', $asset->publishedName());
        $this->assertSame('/media/'.$asset->publishedName(), $asset->publishedUrl());
    }

    public function test_two_assets_with_identical_bytes_publish_to_one_file(): void
    {
        $this->storedAsset('media/one.png');
        $this->storedAsset('media/two.png');

        $result = MediaPublisher::fromConfig()->publish();

        // Same content, same hash, same destination — copied once, skipped once.
        $this->assertCount(1, $result['copied']);
        $this->assertSame(1, $result['skipped']);
        $this->assertCount(1, glob($this->mediaDir().'/*.png'));
    }

    public function test_it_copies_uploads_into_the_frontend(): void
    {
        $asset = $this->storedAsset();

        MediaPublisher::fromConfig()->publish();

        $this->assertFileExists($this->mediaDir().'/'.$asset->publishedName());
    }

    public function test_publishing_twice_does_not_copy_again(): void
    {
        $this->storedAsset();
        $publisher = MediaPublisher::fromConfig();

        $publisher->publish();
        $second = $publisher->publish();

        $this->assertSame([], $second['copied']);
        $this->assertSame(1, $second['skipped']);
    }

    public function test_an_asset_whose_file_vanished_is_reported_not_fatal(): void
    {
        $asset = $this->storedAsset();
        Storage::disk('public')->delete($asset->path);

        $result = MediaPublisher::fromConfig()->publish();

        $this->assertSame([$asset->path], $result['missing']);
        $this->assertSame([], $result['copied']);
    }

    public function test_it_finds_and_prunes_files_no_asset_points_at(): void
    {
        $asset = $this->storedAsset();
        $publisher = MediaPublisher::fromConfig();
        $publisher->publish();

        File::put($this->mediaDir().'/stale-0000.png', 'x');

        $this->assertSame(['stale-0000.png'], $publisher->orphans());

        $removed = $publisher->prune();

        $this->assertSame(['stale-0000.png'], $removed);
        $this->assertFileDoesNotExist($this->mediaDir().'/stale-0000.png');
        // The live one is untouched.
        $this->assertFileExists($this->mediaDir().'/'.$asset->publishedName());
    }

    public function test_prune_only_runs_when_asked(): void
    {
        $this->storedAsset();
        MediaPublisher::fromConfig()->publish();
        File::put($this->mediaDir().'/stale-0000.png', 'x');

        $this->artisan('content:export')->assertSuccessful();
        $this->assertFileExists($this->mediaDir().'/stale-0000.png');

        $this->artisan('content:export', ['--prune' => true])->assertSuccessful();
        $this->assertFileDoesNotExist($this->mediaDir().'/stale-0000.png');
    }

    public function test_it_prunes_uploads_no_asset_points_at(): void
    {
        $kept = $this->storedAsset();
        Storage::disk('public')->put('media/abandoned.png', MediaAssetFactory::onePixelPng());
        $this->travel(2)->hours();

        $publisher = MediaPublisher::fromConfig();

        $this->assertSame(['media/abandoned.png'], $publisher->storageOrphans());

        $this->assertSame(['media/abandoned.png'], $publisher->pruneStorage());
        Storage::disk('public')->assertMissing('media/abandoned.png');
        Storage::disk('public')->assertExists($kept->path);
    }

    public function test_a_just_uploaded_file_survives_a_prune(): void
    {
        /*
         * Filament writes the file when it is chosen, not when the form is
         * saved, so an upload belonging to a form somebody still has open
         * looks exactly like an abandoned one. The grace window is what keeps
         * publishing from deleting their work mid-edit.
         */
        Storage::disk('public')->put('media/being-filled-in.png', MediaAssetFactory::onePixelPng());

        $this->artisan('content:export', ['--prune' => true])->assertSuccessful();

        Storage::disk('public')->assertExists('media/being-filled-in.png');
    }

    public function test_it_warns_about_abandoned_uploads_without_prune(): void
    {
        Storage::disk('public')->put('media/abandoned.png', MediaAssetFactory::onePixelPng());
        $this->travel(2)->hours();

        $this->artisan('content:export')
            ->expectsOutputToContain('1 uploaded file(s) no longer referenced')
            ->assertSuccessful();

        Storage::disk('public')->assertExists('media/abandoned.png');
    }

    public function test_a_post_with_a_picture_exports_its_published_path(): void
    {
        $asset = $this->storedAsset();
        SocialPost::factory()->create([
            'url' => 'https://www.instagram.com/p/ABC12345678/',
            'media_asset_id' => $asset->id,
            'caption_id' => 'Tim di lokasi.',
            'caption_en' => 'The team on site.',
        ]);

        $this->assertSame([[
            'url' => 'https://www.instagram.com/p/ABC12345678/',
            'image' => '/media/'.$asset->publishedName(),
            'width' => 1,
            'height' => 1,
            'caption' => ['id' => 'Tim di lokasi.', 'en' => 'The team on site.'],
        ]], (new SocialPostExporter)->data());
    }

    public function test_a_post_without_a_picture_exports_only_its_url(): void
    {
        SocialPost::factory()->create(['url' => 'https://www.instagram.com/reel/XYZ98765432/']);

        // No `image` key at all, so the frontend falls through to the embed
        // route; no `caption` key, so the panel uses its generic label.
        $this->assertSame(
            [['url' => 'https://www.instagram.com/reel/XYZ98765432/']],
            (new SocialPostExporter)->data(),
        );
    }

    public function test_posts_export_in_position_order(): void
    {
        SocialPost::factory()->create(['url' => 'https://www.instagram.com/p/SECOND00000/', 'position' => 2]);
        SocialPost::factory()->create(['url' => 'https://www.instagram.com/p/FIRST000000/', 'position' => 1]);

        $this->assertSame(
            ['https://www.instagram.com/p/FIRST000000/', 'https://www.instagram.com/p/SECOND00000/'],
            array_column((new SocialPostExporter)->data(), 'url'),
        );
    }

    public function test_deleting_an_asset_leaves_its_post_as_an_embed(): void
    {
        $asset = $this->storedAsset();
        $post = SocialPost::factory()->create(['media_asset_id' => $asset->id]);

        $asset->delete();

        // nullOnDelete, so the panel degrades to the embed route rather than
        // pointing at a picture that is no longer published.
        $this->assertNull($post->refresh()->media_asset_id);
        $this->assertArrayNotHasKey('image', (new SocialPostExporter)->data()[0]);
    }
}
