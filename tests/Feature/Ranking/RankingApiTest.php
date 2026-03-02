<?php

namespace Tests\Feature\Ranking;

use App\Models\PointsTransaction;
use App\Models\User;
use App\Models\UserProgress;
use Database\Seeders\LeagueSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RankingApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(LeagueSeeder::class);
    }

    public function test_add_points_is_idempotent_for_same_source(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $target = User::factory()->create();

        Sanctum::actingAs($admin);

        $payload = [
            'user_id' => $target->id,
            'kind' => PointsTransaction::KIND_RANK,
            'points' => 120,
            'idempotency_key' => 'grant-rank-001',
            'reason' => 'test idempotence',
        ];

        $first = $this->postJson('/api/admin/points/grant', $payload);
        $first->assertOk()->assertJsonPath('idempotent', false);

        $second = $this->postJson('/api/admin/points/grant', $payload);
        $second->assertOk()->assertJsonPath('idempotent', true);

        $this->assertDatabaseCount('points_transactions', 1);

        $progress = UserProgress::query()->findOrFail($target->id);
        $this->assertSame(120, $progress->total_rank_points);
    }

    public function test_rank_points_trigger_automatic_promotion(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $target = User::factory()->create();

        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/admin/points/grant', [
            'user_id' => $target->id,
            'kind' => PointsTransaction::KIND_RANK,
            'points' => 260,
            'idempotency_key' => 'grant-rank-260',
            'reason' => 'promotion flow',
        ]);

        $response->assertOk()->assertJsonPath('idempotent', false);

        $progress = UserProgress::query()->with('league')->findOrFail($target->id);
        $this->assertSame('or', $progress->league?->key);

        $this->assertDatabaseCount('league_promotions', 2);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'ranking.league.promoted',
        ]);
    }

    public function test_leaderboard_is_filtered_by_league_and_sorted(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $topUser = User::factory()->create(['name' => 'Top Player']);
        $secondUser = User::factory()->create(['name' => 'Second Player']);
        $argentUser = User::factory()->create(['name' => 'Argent Player']);

        Sanctum::actingAs($admin);

        $this->grantRankPoints($topUser, 400, 'lb-top');
        $this->grantRankPoints($secondUser, 280, 'lb-second');
        $this->grantRankPoints($argentUser, 120, 'lb-argent');

        $response = $this->getJson('/api/leagues/or/leaderboard');

        $response->assertOk()
            ->assertJsonPath('league.key', 'or')
            ->assertJsonCount(2, 'entries')
            ->assertJsonPath('entries.0.user_id', $topUser->id)
            ->assertJsonPath('entries.1.user_id', $secondUser->id);
    }

    public function test_non_admin_cannot_grant_points(): void
    {
        $regularUser = User::factory()->create(['role' => User::ROLE_USER]);
        $target = User::factory()->create();

        Sanctum::actingAs($regularUser);

        $response = $this->postJson('/api/admin/points/grant', [
            'user_id' => $target->id,
            'kind' => PointsTransaction::KIND_XP,
            'points' => 50,
            'idempotency_key' => 'forbidden-grant',
        ]);

        $response->assertForbidden();
    }

    private function grantRankPoints(User $target, int $points, string $key): void
    {
        $response = $this->postJson('/api/admin/points/grant', [
            'user_id' => $target->id,
            'kind' => PointsTransaction::KIND_RANK,
            'points' => $points,
            'idempotency_key' => $key.'-'.Str::random(4),
        ]);

        $response->assertOk();
    }
}
