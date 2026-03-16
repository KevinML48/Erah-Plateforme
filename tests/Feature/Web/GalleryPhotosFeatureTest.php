<?php

namespace Tests\Feature\Web;

use App\Models\GalleryPhoto;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GalleryPhotosFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_gallery_displays_only_active_and_published_media(): void
    {
        GalleryPhoto::factory()->create([
            'title' => 'Visible media',
            'is_active' => true,
            'published_at' => now()->subMinute(),
        ]);

        GalleryPhoto::factory()->inactive()->create([
            'title' => 'Inactive media',
        ]);

        GalleryPhoto::factory()->unpublished()->create([
            'title' => 'Future media',
        ]);

        $response = $this->get(route('marketing.gallery-photos'));

        $response->assertOk();
        $response->assertSee('Visible media');
        $response->assertDontSee('Inactive media');
        $response->assertDontSee('Future media');
    }

    public function test_admin_gallery_route_is_protected(): void
    {
        $regularUser = User::factory()->create();
        $adminUser = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->get(route('admin.gallery-photos.index'))
            ->assertRedirect(route('login', ['required' => 'participation']));

        $this->actingAs($regularUser)
            ->get(route('admin.gallery-photos.index'))
            ->assertForbidden();

        GalleryPhoto::factory()->create();

        $this->actingAs($adminUser)
            ->get(route('admin.gallery-photos.index'))
            ->assertOk();
    }

    public function test_admin_can_create_gallery_photo_with_upload(): void
    {
        config(['filesystems.media_disk' => 'public']);
        Storage::fake('public');
        $adminUser = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($adminUser)->post(route('admin.gallery-photos.store'), [
            'title' => 'Visuel admin',
            'description' => 'Description galerie',
            'filter_key' => 'valorant',
            'filter_label' => 'Valorant',
            'category_label' => 'Esport',
            'cursor_label' => 'Voir',
            'alt_text' => 'Visuel admin',
            'sort_order' => 4,
            'is_active' => '1',
            'published_at' => now()->format('Y-m-d H:i:s'),
            'media_file' => UploadedFile::fake()->create('visuel.jpg', 120, 'image/jpeg'),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $photo = GalleryPhoto::query()->where('title', 'Visuel admin')->firstOrFail();

        $this->assertSame('valorant', $photo->filter_key);
        $this->assertSame(User::ROLE_ADMIN, $adminUser->role);
        $this->assertNotNull($photo->image_path);
        $this->assertSame('public', $photo->storage_disk);
        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk('public');
        $storage->assertExists($photo->image_path);
    }

    public function test_admin_can_update_gallery_photo_and_toggle_state(): void
    {
        config(['filesystems.media_disk' => 'public']);
        Storage::fake('public');
        $adminUser = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $originalPath = UploadedFile::fake()->create('initial.jpg', 120, 'image/jpeg')->store('gallery/photos', 'public');

        $photo = GalleryPhoto::factory()->create([
            'title' => 'Avant update',
            'image_path' => $originalPath,
            'storage_disk' => 'public',
            'media_type' => GalleryPhoto::MEDIA_TYPE_IMAGE,
        ]);

        $this->actingAs($adminUser)->put(route('admin.gallery-photos.update', $photo->id), [
            'title' => 'Apres update',
            'description' => 'Texte mis a jour',
            'filter_key' => 'competitions',
            'filter_label' => 'Competitions',
            'category_label' => 'COD',
            'cursor_label' => 'Ouvrir',
            'alt_text' => 'Nouvel alt',
            'sort_order' => 9,
            'published_at' => now()->subHour()->format('Y-m-d H:i:s'),
            'media_file' => UploadedFile::fake()->create('remplacement.jpg', 140, 'image/jpeg'),
        ])->assertRedirect();

        $photo->refresh();

        $this->assertSame('Apres update', $photo->title);
        $this->assertSame('competitions', $photo->filter_key);
        $this->assertSame(9, $photo->sort_order);
        $this->assertNotSame($originalPath, $photo->image_path);
        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk('public');
        $storage->assertMissing($originalPath);
        $storage->assertExists($photo->image_path);

        $this->actingAs($adminUser)->post(route('admin.gallery-photos.toggle', $photo->id))
            ->assertRedirect();

        $this->assertFalse($photo->fresh()->is_active);
    }

    public function test_admin_can_reorder_gallery_photos(): void
    {
        $adminUser = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $first = GalleryPhoto::factory()->create([
            'title' => 'Premier',
            'sort_order' => 0,
        ]);
        $second = GalleryPhoto::factory()->create([
            'title' => 'Second',
            'sort_order' => 1,
        ]);
        $third = GalleryPhoto::factory()->create([
            'title' => 'Troisieme',
            'sort_order' => 2,
        ]);

        $this->actingAs($adminUser)
            ->post(route('admin.gallery-photos.reorder', $third->id), ['direction' => 'up'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(0, $first->fresh()->sort_order);
        $this->assertSame(1, $third->fresh()->sort_order);
        $this->assertSame(2, $second->fresh()->sort_order);
    }

    public function test_admin_gallery_can_sort_by_recent_first(): void
    {
        $adminUser = User::factory()->create(['role' => User::ROLE_ADMIN]);

        GalleryPhoto::factory()->create([
            'title' => 'Ancienne photo',
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        GalleryPhoto::factory()->create([
            'title' => 'Nouvelle photo',
            'created_at' => now()->subHour(),
            'updated_at' => now()->subHour(),
        ]);

        $this->actingAs($adminUser)
            ->get(route('admin.gallery-photos.index', ['sort' => 'recent']))
            ->assertOk()
            ->assertSeeInOrder(['Nouvelle photo', 'Ancienne photo']);
    }

    public function test_admin_can_delete_gallery_photo_and_uploaded_file(): void
    {
        config(['filesystems.media_disk' => 'public']);
        Storage::fake('public');
        $adminUser = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $path = UploadedFile::fake()->create('delete-me.jpg', 90, 'image/jpeg')->store('gallery/photos', 'public');

        $photo = GalleryPhoto::factory()->create([
            'image_path' => $path,
            'storage_disk' => 'public',
            'media_type' => GalleryPhoto::MEDIA_TYPE_IMAGE,
        ]);

        $this->actingAs($adminUser)
            ->delete(route('admin.gallery-photos.destroy', $photo->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('gallery_photos', ['id' => $photo->id]);
        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk('public');
        $storage->assertMissing($path);
    }

    public function test_gallery_photo_uses_public_media_route_for_public_disk_assets(): void
    {
        config(['filesystems.media_disk' => 'public']);
        Storage::fake('public');

        Storage::disk('public')->put('gallery/photos/public-photo.jpg', 'image');

        $photo = GalleryPhoto::factory()->create([
            'image_path' => 'gallery/photos/public-photo.jpg',
            'storage_disk' => 'public',
            'media_type' => GalleryPhoto::MEDIA_TYPE_IMAGE,
        ]);

        $this->assertSame(route('media.public.file', ['path' => 'gallery/photos/public-photo.jpg']), $photo->image_url);

        $this->get(route('media.public.file', ['path' => 'gallery/photos/public-photo.jpg']))
            ->assertOk();
    }

    public function test_legacy_gallery_import_command_is_idempotent(): void
    {
        Artisan::call('gallery-photos:import-legacy');
        $countAfterFirstImport = GalleryPhoto::query()->count();

        Artisan::call('gallery-photos:import-legacy');
        $countAfterSecondImport = GalleryPhoto::query()->count();

        $this->assertGreaterThan(0, $countAfterFirstImport);
        $this->assertSame($countAfterFirstImport, $countAfterSecondImport);
    }
}
