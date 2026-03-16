<?php

namespace Tests\Feature\Media;

use App\Models\GalleryPhoto;
use App\Models\GalleryVideo;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MigrateLegacyMediaToMediaDiskCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_migrates_legacy_public_media_to_the_target_disk(): void
    {
        Storage::fake('public');
        Storage::fake('s3');

        Config::set('filesystems.media_disk', 's3');

        Storage::disk('public')->put('avatars/legacy-user.png', 'avatar-content');
        Storage::disk('public')->put('gallery/photos/legacy-photo.jpg', 'photo-content');
        Storage::disk('public')->put('gallery-videos/thumbnails/legacy-thumb.jpg', 'thumb-content');
        Storage::disk('public')->put('gallery-videos/previews/legacy-preview.mp4', 'preview-content');

        $user = User::factory()->create([
            'avatar_path' => 'avatars/legacy-user.png',
        ]);

        $photo = GalleryPhoto::factory()->create([
            'image_path' => 'gallery/photos/legacy-photo.jpg',
            'storage_disk' => 'public',
        ]);

        $video = GalleryVideo::factory()->create([
            'thumbnail_url' => 'gallery-videos/thumbnails/legacy-thumb.jpg',
            'preview_video_url' => 'gallery-videos/previews/legacy-preview.mp4',
        ]);

        $exitCode = Artisan::call('media:migrate-legacy-public-to-media-disk');

        self::assertSame(0, $exitCode);

    /** @var FilesystemAdapter $storage */
    $storage = Storage::disk('s3');
    $storage->assertExists('avatars/legacy-user.png');
    $storage->assertExists('gallery/photos/legacy-photo.jpg');
    $storage->assertExists('gallery-videos/thumbnails/legacy-thumb.jpg');
    $storage->assertExists('gallery-videos/previews/legacy-preview.mp4');

        self::assertSame('avatar-content', Storage::disk('s3')->get('avatars/legacy-user.png'));
        self::assertSame('photo-content', Storage::disk('s3')->get('gallery/photos/legacy-photo.jpg'));

        self::assertSame('avatars/legacy-user.png', $user->fresh()->avatar_path);
        self::assertSame('s3', $photo->fresh()->storage_disk);
        self::assertSame('gallery-videos/thumbnails/legacy-thumb.jpg', $video->fresh()->thumbnail_url);
    }

    public function test_it_skips_records_when_source_file_is_missing(): void
    {
        Storage::fake('public');
        Storage::fake('s3');

        Config::set('filesystems.media_disk', 's3');

        $photo = GalleryPhoto::factory()->create([
            'image_path' => 'gallery/photos/missing-photo.jpg',
            'storage_disk' => 'public',
        ]);

        $exitCode = Artisan::call('media:migrate-legacy-public-to-media-disk');

        self::assertSame(0, $exitCode);
        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk('s3');
        $storage->assertMissing('gallery/photos/missing-photo.jpg');
        self::assertSame('public', $photo->fresh()->storage_disk);
    }

    public function test_it_updates_db_without_recopying_when_target_already_exists(): void
    {
        Storage::fake('public');
        Storage::fake('s3');

        Config::set('filesystems.media_disk', 's3');

        Storage::disk('public')->put('gallery/photos/already-migrated.jpg', 'old-content');
        Storage::disk('s3')->put('gallery/photos/already-migrated.jpg', 'new-content');

        $photo = GalleryPhoto::factory()->create([
            'image_path' => 'gallery/photos/already-migrated.jpg',
            'storage_disk' => 'public',
        ]);

        $exitCode = Artisan::call('media:migrate-legacy-public-to-media-disk');

        self::assertSame(0, $exitCode);
        self::assertSame('new-content', Storage::disk('s3')->get('gallery/photos/already-migrated.jpg'));
        self::assertSame('s3', $photo->fresh()->storage_disk);
    }
}