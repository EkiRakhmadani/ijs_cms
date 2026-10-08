<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\MediaAssets\Pages\ListMediaAssets;
use App\Filament\Resources\SocialPosts\Pages\EditSocialPost;
use App\Filament\Resources\SocialPosts\Pages\ListSocialPosts;
use App\Models\MediaAsset;
use App\Models\SocialPost;
use App\Models\User;
use Database\Factories\MediaAssetFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class MediaPanelTest extends TestCase
{
    use RefreshDatabase;

    private string $frontend;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->frontend = storage_path('framework/testing/frontend-mediapanel-'.getmypid());
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

    private function storedAsset(): MediaAsset
    {
        Storage::disk('public')->put('media/post.png', MediaAssetFactory::onePixelPng());

        return MediaAsset::create(['path' => 'media/post.png', 'original_name' => 'post.png']);
    }

    public function test_the_media_list_renders_with_an_asset(): void
    {
        $asset = $this->storedAsset();

        Livewire::test(ListMediaAssets::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$asset]);
    }

    public function test_preview_urls_do_not_depend_on_app_url(): void
    {
        /*
         * APP_URL carries no port in this project, so a storage URL built
         * from it would point at port 80 and every preview in the panel
         * would break. Root-relative is what keeps them working on whatever
         * port the app happened to bind.
         */
        config()->set('app.url', 'http://localhost');

        $url = Storage::disk('public')->url('media/post.png');

        $this->assertSame('/storage/media/post.png', $url);
        $this->assertStringStartsNotWith('http', $url);
    }

    public function test_the_social_post_list_renders_both_panel_kinds(): void
    {
        $withPicture = SocialPost::factory()->create(['media_asset_id' => $this->storedAsset()->id, 'position' => 1]);
        $embedOnly = SocialPost::factory()->create(['position' => 2]);

        Livewire::test(ListSocialPosts::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$withPicture, $embedOnly], inOrder: true);
    }

    public function test_it_rejects_a_url_that_is_not_an_instagram_post(): void
    {
        $post = SocialPost::factory()->create();

        Livewire::test(EditSocialPost::class, ['record' => $post->getKey()])
            ->fillForm(['url' => 'https://example.com/nope'])
            ->call('save')
            ->assertHasFormErrors(['url']);
    }

    public function test_it_accepts_reel_and_tv_permalinks_too(): void
    {
        $post = SocialPost::factory()->create();

        Livewire::test(EditSocialPost::class, ['record' => $post->getKey()])
            ->fillForm(['url' => 'https://www.instagram.com/reel/ABC12345678/'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('https://www.instagram.com/reel/ABC12345678/', $post->refresh()->url);
    }

    public function test_publishing_from_the_media_screen_copies_the_file(): void
    {
        $asset = $this->storedAsset();

        Livewire::test(ListMediaAssets::class)
            ->callAction('publishToFrontend');

        $this->assertFileExists($this->frontend.'/public/media/'.$asset->publishedName());
    }
}
