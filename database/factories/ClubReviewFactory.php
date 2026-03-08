<?php

namespace Database\Factories;

use App\Models\ClubReview;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClubReview>
 */
class ClubReviewFactory extends Factory
{
    protected $model = ClubReview::class;

    public function definition(): array
    {
        return [
            'user_id' => null,
            'author_name' => fake()->name(),
            'author_profile_url' => fake()->optional()->url(),
            'content' => fake()->paragraphs(2, true),
            'status' => ClubReview::STATUS_PUBLISHED,
            'is_featured' => false,
            'source' => ClubReview::SOURCE_SEED,
            'display_order' => null,
            'published_at' => now(),
        ];
    }

    public function member(?User $user = null): static
    {
        return $this->state(function () use ($user): array {
            $member = $user ?: User::factory()->create();

            return [
                'user_id' => $member->id,
                'author_name' => null,
                'author_profile_url' => null,
                'source' => ClubReview::SOURCE_MEMBER,
            ];
        });
    }

    public function hidden(): static
    {
        return $this->state([
            'status' => ClubReview::STATUS_HIDDEN,
            'published_at' => now()->subDay(),
        ]);
    }

    public function draft(): static
    {
        return $this->state([
            'status' => ClubReview::STATUS_DRAFT,
            'published_at' => null,
        ]);
    }
}
