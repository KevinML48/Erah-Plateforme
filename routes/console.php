<?php

use App\Jobs\RebuildLeaderboardStatsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new RebuildLeaderboardStatsJob())
    ->hourly()
    ->name('leaderboard-stats-hourly-rebuild');
