<?php

namespace Database\Factories;

use App\Models\GalleryVideo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GalleryVideo>
 */
class GalleryVideoFactory extends Factory
{
    protected $model = GalleryVideo::class;

    public function definition(): array
    {
        $title = fake()->sentence(3);
        $videoUrl = 'https://youtu.be/MCh7wI7gMOU';

        return [
            'title' => $title,
            'slug' => GalleryVideo::uniqueSlug($title),
            'excerpt' => fake()->sentence(10),
            'description' => fake()->paragraphs(2, true),
            'platform' => GalleryVideo::PLATFORM_YOUTUBE,
            'video_url' => $videoUrl,
            'embed_url' => GalleryVideo::buildEmbedUrl($videoUrl, GalleryVideo::PLATFORM_YOUTUBE),
            'thumbnail_url' => GalleryVideo::buildThumbnailUrl($videoUrl, GalleryVideo::PLATFORM_YOUTUBE),
            'preview_video_url' => '/template/assets/vids/Presentation ERAH.mp4',
            'preview_video_webm_url' => '/template/assets/vids/Presentation ERAH.webm',
            'category_key' => 'club',
            'category_label' => 'Club',
            'status' => GalleryVideo::STATUS_PUBLISHED,
            'sort_order' => fake()->numberBetween(0, 50),
            'is_featured' => false,
            'published_at' => now()->subHour(),
            'legacy_source' => null,
            'imported_hash' => null,
            'created_by' => null,
            'updated_by' => null,
        ];
    }

    public function draft(): self
    {
        return $this->state(fn (): array => [
            'status' => GalleryVideo::STATUS_DRAFT,
            'published_at' => null,
        ]);
    }

    public function archived(): self
    {
        return $this->state(fn (): array => [
            'status' => GalleryVideo::STATUS_ARCHIVED,
            'published_at' => null,
        ]);
    }

    public function featured(): self
    {
        return $this->state(fn (): array => [
            'is_featured' => true,
            'sort_order' => 0,
        ]);
    }
}