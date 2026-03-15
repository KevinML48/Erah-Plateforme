<?php

namespace Tests\Feature\Web;

use App\Models\GalleryVideo;
use App\Models\User;
use Database\Seeders\GalleryVideoLegacySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GalleryVideosFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_gallery_imports_legacy_template_when_empty(): void
    {
        $response = $this->get(route('marketing.gallery-video'));

        $response->assertOk();
        $response->assertSee('Présentation');
        $response->assertSee('VCL Split 1 2026');
        $this->assertGreaterThan(0, GalleryVideo::query()->count());
    }

    public function test_public_gallery_displays_only_published_videos(): void
    {
        GalleryVideo::factory()->create([
            'title' => 'Video publique',
            'status' => GalleryVideo::STATUS_PUBLISHED,
            'published_at' => now()->subMinute(),
        ]);

        GalleryVideo::factory()->draft()->create([
            'title' => 'Video brouillon',
        ]);

        GalleryVideo::factory()->archived()->create([
            'title' => 'Video archivee',
        ]);

        $response = $this->get(route('marketing.gallery-video'));

        $response->assertOk();
        $response->assertSee('Video publique');
        $response->assertDontSee('Video brouillon');
        $response->assertDontSee('Video archivee');
    }

    public function test_public_gallery_can_filter_by_category_from_url(): void
    {
        GalleryVideo::factory()->create([
            'title' => 'Video Valorant',
            'category_key' => 'valorant',
            'category_label' => 'Valorant',
            'status' => GalleryVideo::STATUS_PUBLISHED,
            'published_at' => now()->subMinute(),
        ]);

        GalleryVideo::factory()->create([
            'title' => 'Video LAN',
            'category_key' => 'lan',
            'category_label' => 'LAN',
            'status' => GalleryVideo::STATUS_PUBLISHED,
            'published_at' => now()->subMinute(),
        ]);

        $response = $this->get(route('marketing.gallery-video', ['category' => 'lan']));

        $response->assertOk();
        $response->assertSee('Video LAN');
        $response->assertSee('Video Valorant');
    }

    public function test_public_gallery_uses_published_admin_videos_in_slider(): void
    {
        GalleryVideo::factory()->create([
            'title' => 'Video a la une',
            'is_featured' => true,
            'category_key' => 'club',
            'category_label' => 'Club',
            'status' => GalleryVideo::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
        ]);

        GalleryVideo::factory()->create([
            'title' => 'Recap LAN',
            'category_key' => 'lan',
            'category_label' => 'LAN',
            'status' => GalleryVideo::STATUS_PUBLISHED,
            'published_at' => now()->subHours(2),
        ]);

        $response = $this->get(route('marketing.gallery-video'));

        $response->assertOk();
        $response->assertSee('Video a la une');
        $response->assertSee('Recap LAN');
        $response->assertSee('Voir la vidéo');
    }

    public function test_admin_gallery_video_route_is_protected(): void
    {
        $regularUser = User::factory()->create();
        $adminUser = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->get(route('admin.gallery-videos.index'))
            ->assertRedirect(route('login', ['required' => 'participation']));

        $this->actingAs($regularUser)
            ->get(route('admin.gallery-videos.index'))
            ->assertForbidden();

        $this->actingAs($adminUser)
            ->get(route('admin.gallery-videos.index'))
            ->assertOk();

        $this->actingAs($regularUser)
            ->post(route('admin.gallery-videos.import-legacy'))
            ->assertForbidden();

        $this->actingAs($regularUser)
            ->post(route('admin.gallery-videos.import-legacy-if-empty'))
            ->assertForbidden();
    }

    public function test_admin_can_trigger_legacy_import_from_console(): void
    {
        $adminUser = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($adminUser)
            ->post(route('admin.gallery-videos.import-legacy'))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertGreaterThan(0, GalleryVideo::query()->count());

        $response = $this->actingAs($adminUser)
            ->get(route('admin.gallery-videos.index'));

        $response->assertOk();
        $response->assertSee('Template historique');
        $response->assertSee('Legacy');
    }

    public function test_admin_can_trigger_legacy_import_only_when_gallery_is_empty(): void
    {
        $adminUser = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($adminUser)
            ->post(route('admin.gallery-videos.import-legacy-if-empty'))
            ->assertRedirect()
            ->assertSessionHas('success');

        $countAfterFirstImport = GalleryVideo::query()->count();

        $this->actingAs($adminUser)
            ->post(route('admin.gallery-videos.import-legacy-if-empty'))
            ->assertRedirect()
            ->assertSessionHas('info');

        $this->assertSame($countAfterFirstImport, GalleryVideo::query()->count());
    }

    public function test_admin_can_filter_legacy_videos_only(): void
    {
        $adminUser = User::factory()->create(['role' => User::ROLE_ADMIN]);

        GalleryVideo::factory()->create([
            'title' => 'Video legacy',
            'legacy_source' => '_template_site/galerie-video.html',
        ]);

        GalleryVideo::factory()->create([
            'title' => 'Video manuelle',
            'legacy_source' => null,
        ]);

        $response = $this->actingAs($adminUser)
            ->get(route('admin.gallery-videos.index', ['source' => 'legacy']));

        $response->assertOk();
        $response->assertSee('Video legacy');
        $response->assertDontSee('Video manuelle');
    }

    public function test_admin_can_create_update_and_change_video_status(): void
    {
        $adminUser = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Storage::fake('public');

        $this->actingAs($adminUser)->post(route('admin.gallery-videos.store'), [
            'title' => 'Interview ERAH',
            'slug' => 'interview-erah',
            'excerpt' => 'Une accroche claire',
            'description' => 'Description complete',
            'platform' => 'youtube',
            'video_url' => 'https://youtu.be/n_LEo-tp3Jk',
            'thumbnail_image' => $this->fakeThumbnail('thumb-one.png'),
            'category_key' => 'lan',
            'category_label' => 'LAN',
            'status' => 'draft',
            'sort_order' => 3,
            'is_featured' => '1',
        ])->assertRedirect()->assertSessionHas('success');

        $video = GalleryVideo::query()->where('slug', 'interview-erah')->firstOrFail();

        $this->assertSame('youtube', $video->platform);
        $this->assertSame(GalleryVideo::STATUS_DRAFT, $video->status);
        $this->assertTrue($video->is_featured);
        $this->assertNotNull($video->embed_url);
        $this->assertNotNull($video->thumbnail_url);
        $this->assertStringStartsWith('/storage/gallery-videos/thumbnails/', $video->thumbnail_url);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $video->thumbnail_url));

        $storedThumbnail = $video->thumbnail_url;

        $this->actingAs($adminUser)->put(route('admin.gallery-videos.update', $video->id), [
            'title' => 'Interview ERAH MAJ',
            'slug' => 'interview-erah-maj',
            'excerpt' => 'Accroche revue',
            'description' => 'Description revue',
            'platform' => 'youtube',
            'video_url' => 'https://youtu.be/6-ebq2tKpAs',
            'thumbnail_image' => $this->fakeThumbnail('thumb-two.png'),
            'category_key' => 'event',
            'category_label' => 'Event',
            'status' => 'published',
            'sort_order' => 1,
            'published_at' => now()->subHour()->format('Y-m-d H:i:s'),
        ])->assertRedirect()->assertSessionHas('success');

        $video->refresh();

        $this->assertSame('Interview ERAH MAJ', $video->title);
        $this->assertSame('interview-erah-maj', $video->slug);
        $this->assertSame(GalleryVideo::STATUS_PUBLISHED, $video->status);
        $this->assertSame(1, $video->sort_order);
        $this->assertNotSame($storedThumbnail, $video->thumbnail_url);
        Storage::disk('public')->assertMissing(str_replace('/storage/', '', $storedThumbnail));
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $video->thumbnail_url));

        $this->actingAs($adminUser)
            ->post(route('admin.gallery-videos.archive', $video->id))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(GalleryVideo::STATUS_ARCHIVED, $video->fresh()->status);

        $this->actingAs($adminUser)
            ->post(route('admin.gallery-videos.unpublish', $video->id))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(GalleryVideo::STATUS_DRAFT, $video->fresh()->status);

        $this->actingAs($adminUser)
            ->post(route('admin.gallery-videos.publish', $video->id))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(GalleryVideo::STATUS_PUBLISHED, $video->fresh()->status);
    }

    public function test_admin_can_remove_thumbnail_without_deleting_video(): void
    {
        $adminUser = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Storage::fake('public');

        $storedPath = UploadedFile::fake()->createWithContent(
            'gallery-thumb.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9sF2r4YAAAAASUVORK5CYII=')
        )->store('gallery-videos/thumbnails', 'public');

        $video = GalleryVideo::factory()->create([
            'thumbnail_url' => Storage::url($storedPath),
        ]);

        $this->actingAs($adminUser)
            ->post(route('admin.gallery-videos.remove-thumbnail', $video->id))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('gallery_videos', [
            'id' => $video->id,
            'thumbnail_url' => null,
        ]);
        Storage::disk('public')->assertMissing($storedPath);
    }

    public function test_admin_can_reorder_and_delete_gallery_videos(): void
    {
        $adminUser = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $first = GalleryVideo::factory()->create([
            'title' => 'Premier',
            'sort_order' => 0,
            'is_featured' => true,
        ]);
        $second = GalleryVideo::factory()->create([
            'title' => 'Second',
            'sort_order' => 1,
            'is_featured' => false,
        ]);
        $third = GalleryVideo::factory()->create([
            'title' => 'Troisieme',
            'sort_order' => 2,
            'is_featured' => false,
        ]);

        $this->actingAs($adminUser)
            ->post(route('admin.gallery-videos.reorder', $third->id), ['direction' => 'top'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(0, $third->fresh()->sort_order);
        $this->assertTrue($third->fresh()->is_featured);
        $this->assertFalse($first->fresh()->is_featured);

        $this->actingAs($adminUser)
            ->delete(route('admin.gallery-videos.destroy', $second->id))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('gallery_videos', ['id' => $second->id]);
    }

    public function test_legacy_gallery_video_import_command_is_idempotent(): void
    {
        Artisan::call('gallery-videos:import-legacy');
        $countAfterFirstImport = GalleryVideo::query()->count();

        Artisan::call('gallery-videos:import-legacy');
        $countAfterSecondImport = GalleryVideo::query()->count();

        $this->assertGreaterThan(0, $countAfterFirstImport);
        $this->assertSame($countAfterFirstImport, $countAfterSecondImport);
    }

    public function test_legacy_gallery_video_seeder_imports_template_videos(): void
    {
        $this->seed(GalleryVideoLegacySeeder::class);

        $this->assertGreaterThan(0, GalleryVideo::query()->count());
        $this->assertDatabaseHas('gallery_videos', [
            'title' => 'Présentation',
            'legacy_source' => '_template_site/galerie-video.html',
        ]);
    }

    private function fakeThumbnail(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $name,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9sF2r4YAAAAASUVORK5CYII=')
        );
    }
}