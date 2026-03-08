<?php

namespace Database\Seeders;

use App\Models\ClubReview;
use App\Support\ClubReviewCatalog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ClubReviewSeeder extends Seeder
{
    public function run(): void
    {
        $baseTimestamp = Carbon::create(2026, 3, 8, 12, 0, 0);

        foreach (ClubReviewCatalog::legacyReviews() as $index => $review) {
            ClubReview::query()->updateOrCreate(
                [
                    'source' => ClubReview::SOURCE_SEED,
                    'display_order' => $index + 1,
                ],
                [
                    'user_id' => null,
                    'author_name' => $review['author_name'],
                    'author_profile_url' => $review['author_profile_url'],
                    'content' => $review['content'],
                    'status' => ClubReview::STATUS_PUBLISHED,
                    'is_featured' => false,
                    'published_at' => $baseTimestamp->copy()->subMinutes($index),
                ]
            );
        }
    }
}
