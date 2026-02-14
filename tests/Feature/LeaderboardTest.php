<?php
declare(strict_types=1);

use App\Models\Rank;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns all-time leaderboard sorted by points desc', function (): void {
    $rank = Rank::query()->create([
        'name' => 'Bronze',
        'slug' => 'bronze',
        'min_points' => 0,
    ]);

    User::factory()->create([
        'name' => 'Low',
        'points_balance' => 100,
        'rank_id' => $rank->id,
    ]);

    User::factory()->create([
        'name' => 'High',
        'points_balance' => 900,
        'rank_id' => $rank->id,
    ]);

    User::factory()->create([
        'name' => 'Mid',
        'points_balance' => 450,
        'rank_id' => $rank->id,
    ]);

    $response = $this->getJson(route('leaderboard.all-time'));

    $response
        ->assertOk()
        ->assertJsonPath('type', 'all_time')
        ->assertJsonPath('data.0.name', 'High')
        ->assertJsonPath('data.1.name', 'Mid')
        ->assertJsonPath('data.2.name', 'Low');
});
