<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            LeagueSeeder::class,
            DemoDataSeeder::class,
            BettingBaseSeeder::class,
            MissionsAndGiftsSeeder::class,
            SupporterProgramSeeder::class,
            ClubReviewSeeder::class,
            CommunityPlatformSeeder::class,
            HelpCenterSeeder::class,
        ]);
    }
}
