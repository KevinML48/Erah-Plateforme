<?php

namespace Database\Factories;

use App\Models\Clip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Clip>
 */
class ClipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::lower(Str::random(6)),
            'description' => fake()->paragraph(),
            'video_url' => fake()->url(),
            'thumbnail_url' => fake()->imageUrl(640, 360),
            'is_published' => true,
            'published_at' => now(),
            'likes_count' => 0,
            'favorites_count' => 0,
            'comments_count' => 0,
            'created_by' => User::factory(),
            'updated_by' => null,
        ];
    }

    public function unpublished(): static
    {
        return $this->state(fn () => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }
}
