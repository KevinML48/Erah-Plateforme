<?php

namespace Tests\Feature\Bets;

use App\Models\Bet;
use App\Models\EsportMatch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BetApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_match_and_public_can_list_it(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        Sanctum::actingAs($user);
        $this->postJson('/api/admin/matches', [
            'match_key' => 'mch-2026-0001',
            'home_team' => 'Team Alpha',
            'away_team' => 'Team Beta',
            'starts_at' => now()->addHours(2)->toIso8601String(),
        ])->assertForbidden();

        Sanctum::actingAs($admin);
        $create = $this->postJson('/api/admin/matches', [
            'match_key' => 'mch-2026-0001',
            'home_team' => 'Team Alpha',
            'away_team' => 'Team Beta',
            'starts_at' => now()->addHours(2)->toIso8601String(),
            'meta' => ['competition' => 'ERAH Cup'],
        ]);

        $create->assertCreated()
            ->assertJsonPath('data.match_key', 'mch-2026-0001')
            ->assertJsonPath('data.status', EsportMatch::STATUS_SCHEDULED);

        $list = $this->getJson('/api/matches');
        $list->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.match_key', 'mch-2026-0001');

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'matches.created',
            'actor_id' => $admin->id,
        ]);
    }

    public function test_user_can_place_bet_idempotently_and_only_once_per_match(): void
    {
        $user = User::factory()->create();
        $match = EsportMatch::factory()->create([
            'starts_at' => now()->addHours(4),
            'status' => EsportMatch::STATUS_SCHEDULED,
            'settled_at' => null,
        ]);

        Sanctum::actingAs($user);

        $payload = [
            'match_id' => $match->id,
            'prediction' => Bet::PREDICTION_HOME,
            'stake_points' => 250,
            'idempotency_key' => 'bet-place-001',
        ];

        $first = $this->postJson('/api/bets', $payload);
        $first->assertCreated()
            ->assertJsonPath('idempotent', false)
            ->assertJsonPath('data.status', Bet::STATUS_PENDING)
            ->assertJsonPath('data.potential_payout', 500);

        $second = $this->postJson('/api/bets', $payload);
        $second->assertOk()
            ->assertJsonPath('idempotent', true);

        $third = $this->postJson('/api/bets', [
            'match_id' => $match->id,
            'prediction' => Bet::PREDICTION_AWAY,
            'stake_points' => 100,
            'idempotency_key' => 'bet-place-002',
        ]);
        $third->assertStatus(422)
            ->assertJsonPath('message', 'Bet already exists for this match.');

        $this->assertDatabaseCount('bets', 1);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'bets.placed',
            'actor_id' => $user->id,
        ]);
    }

    public function test_settlement_is_admin_only_and_idempotent(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $homeUser = User::factory()->create();
        $awayUser = User::factory()->create();

        $match = EsportMatch::factory()->create([
            'match_key' => 'mch-settle-001',
            'home_team' => 'Home Team',
            'away_team' => 'Away Team',
            'starts_at' => now()->addHours(2),
            'status' => EsportMatch::STATUS_SCHEDULED,
            'settled_at' => null,
            'result' => null,
            'created_by' => $admin->id,
        ]);

        Sanctum::actingAs($homeUser);
        $this->postJson('/api/bets', [
            'match_id' => $match->id,
            'prediction' => Bet::PREDICTION_HOME,
            'stake_points' => 100,
            'idempotency_key' => 'bet-home-001',
        ])->assertCreated();

        Sanctum::actingAs($awayUser);
        $this->postJson('/api/bets', [
            'match_id' => $match->id,
            'prediction' => Bet::PREDICTION_AWAY,
            'stake_points' => 70,
            'idempotency_key' => 'bet-away-001',
        ])->assertCreated();

        Sanctum::actingAs($homeUser);
        $this->postJson('/api/admin/matches/'.$match->id.'/settle', [
            'result' => EsportMatch::RESULT_HOME,
            'idempotency_key' => 'settle-001',
        ])->assertForbidden();

        Sanctum::actingAs($admin);
        $first = $this->postJson('/api/admin/matches/'.$match->id.'/settle', [
            'result' => EsportMatch::RESULT_HOME,
            'idempotency_key' => 'settle-001',
        ]);

        $first->assertOk()
            ->assertJsonPath('idempotent', false)
            ->assertJsonPath('settlement.won_count', 1)
            ->assertJsonPath('settlement.lost_count', 1)
            ->assertJsonPath('settlement.void_count', 0)
            ->assertJsonPath('match.status', EsportMatch::STATUS_FINISHED)
            ->assertJsonPath('match.result', EsportMatch::RESULT_HOME);

        $second = $this->postJson('/api/admin/matches/'.$match->id.'/settle', [
            'result' => EsportMatch::RESULT_HOME,
            'idempotency_key' => 'settle-001',
        ]);
        $second->assertOk()->assertJsonPath('idempotent', true);

        $third = $this->postJson('/api/admin/matches/'.$match->id.'/settle', [
            'result' => EsportMatch::RESULT_HOME,
            'idempotency_key' => 'settle-002',
        ]);
        $third->assertStatus(422)->assertJsonPath('message', 'Match already settled.');

        $this->assertDatabaseCount('match_settlements', 1);
        $this->assertDatabaseHas('bets', [
            'user_id' => $homeUser->id,
            'match_id' => $match->id,
            'status' => Bet::STATUS_WON,
            'settlement_points' => 200,
        ]);
        $this->assertDatabaseHas('bets', [
            'user_id' => $awayUser->id,
            'match_id' => $match->id,
            'status' => Bet::STATUS_LOST,
            'settlement_points' => 0,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'matches.settled',
            'actor_id' => $admin->id,
        ]);

        Sanctum::actingAs($homeUser);
        $myWon = $this->getJson('/api/bets/me?status=won');
        $myWon->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', Bet::STATUS_WON);
    }

    public function test_void_settlement_marks_bets_as_void_with_refund_points(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create();

        $match = EsportMatch::factory()->create([
            'starts_at' => now()->addHours(2),
            'status' => EsportMatch::STATUS_SCHEDULED,
            'created_by' => $admin->id,
        ]);

        Sanctum::actingAs($user);
        $this->postJson('/api/bets', [
            'match_id' => $match->id,
            'prediction' => Bet::PREDICTION_DRAW,
            'stake_points' => 45,
            'idempotency_key' => 'bet-void-001',
        ])->assertCreated();

        Sanctum::actingAs($admin);
        $settle = $this->postJson('/api/admin/matches/'.$match->id.'/settle', [
            'result' => EsportMatch::RESULT_VOID,
            'idempotency_key' => 'settle-void-001',
        ]);

        $settle->assertOk()
            ->assertJsonPath('idempotent', false)
            ->assertJsonPath('settlement.void_count', 1);

        $this->assertDatabaseHas('bets', [
            'user_id' => $user->id,
            'match_id' => $match->id,
            'status' => Bet::STATUS_VOID,
            'settlement_points' => 45,
        ]);
    }
}
