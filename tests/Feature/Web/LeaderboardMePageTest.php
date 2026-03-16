<?php

namespace Tests\Feature\Web;

use App\Models\League;
use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaderboardMePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_render_personal_league_page(): void
    {
        $bronze = League::query()->updateOrCreate(
            ['key' => 'bronze'],
            [
                'name' => 'Bronze',
                'min_rank_points' => 0,
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        League::query()->updateOrCreate(
            ['key' => 'argent'],
            [
                'name' => 'Argent',
                'min_rank_points' => 1000,
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        $user = User::factory()->create([
            'name' => 'Test Ligue',
        ]);

        $otherUser = User::factory()->create([
            'name' => 'Autre Joueur',
        ]);

        UserProgress::query()->create([
            'user_id' => $user->id,
            'current_league_id' => $bronze->id,
            'total_xp' => 250,
            'total_rank_points' => 180,
            'last_points_at' => now(),
        ]);

        UserProgress::query()->create([
            'user_id' => $otherUser->id,
            'current_league_id' => $bronze->id,
            'total_xp' => 180,
            'total_rank_points' => 120,
            'last_points_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('leaderboards.me'))
            ->assertOk()
            ->assertSeeText('Ma ligue: Bronze')
            ->assertSeeText('Test Ligue')
            ->assertSeeText('Autre Joueur');
    }
}