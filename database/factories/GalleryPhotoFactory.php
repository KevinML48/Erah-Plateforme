<?php

namespace Database\Factories;

use App\Models\GalleryPhoto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GalleryPhoto>
 */
class GalleryPhotoFactory extends Factory
{
    protected $model = GalleryPhoto::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->sentence(10),
            'image_path' => '/template/assets/img/galerie/challengers_valorant.jpg',
            'video_path' => null,
            'media_type' => GalleryPhoto::MEDIA_TYPE_IMAGE,
            'alt_text' => fake()->sentence(4),
            'filter_key' => 'valorant',
            'filter_label' => 'Valorant',
            'category_label' => 'Esport',
            'cursor_label' => 'Voir',
            'sort_order' => fake()->numberBetween(0, 50),
            'is_active' => true,
            'published_at' => now()->subDay(),
            'storage_disk' => null,
            'media_mime_type' => 'image/jpeg',
            'media_size' => 1024,
            'legacy_source' => null,
            'imported_hash' => null,
            'created_by' => null,
            'updated_by' => null,
        ];
    }

    public function inactive(): self
    {
        return $this->state(fn (): array => [
            'is_active' => false,
        ]);
    }

    public function unpublished(): self
    {
        return $this->state(fn (): array => [
            'published_at' => now()->addDay(),
        ]);
    }

    public function video(): self
    {
        return $this->state(fn (): array => [
            'image_path' => null,
            'video_path' => '/template/assets/vids/interview-HopLan-2025.webm',
            'media_type' => GalleryPhoto::MEDIA_TYPE_VIDEO,
            'media_mime_type' => 'video/webm',
        ]);
    }
}
