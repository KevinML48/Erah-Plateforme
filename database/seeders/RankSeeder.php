<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Rank;
use Illuminate\Database\Seeder;

class RankSeeder extends Seeder
{
    public function run(): void
    {
        $ranks = [
            ['name' => 'Bronze', 'slug' => 'bronze', 'min_points' => 0, 'badge_color' => '#CD7F32'],
            ['name' => 'Silver', 'slug' => 'silver', 'min_points' => 1000, 'badge_color' => '#C0C0C0'],
            ['name' => 'Gold', 'slug' => 'gold', 'min_points' => 3000, 'badge_color' => '#FFD700'],
            ['name' => 'Elite', 'slug' => 'elite', 'min_points' => 7000, 'badge_color' => '#3B82F6'],
            ['name' => 'Champion', 'slug' => 'champion', 'min_points' => 15000, 'badge_color' => '#8B5CF6'],
        ];

        foreach ($ranks as $rank) {
            Rank::query()->updateOrCreate(
                ['slug' => $rank['slug']],
                $rank
            );
        }
    }
}

