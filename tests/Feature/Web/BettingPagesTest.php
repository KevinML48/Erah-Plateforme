<?php

namespace Tests\Feature\Web;

use App\Models\Bet;
use App\Models\EsportMatch;
use App\Models\MatchMarket;
use App\Models\MatchSelection;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BettingPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_match_detail_preselects_first_option_in_bet_form(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $match = EsportMatch::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
            'status' => EsportMatch::STATUS_SCHEDULED,
            'starts_at' => now()->addHours(2),
            'locked_at' => now()->addHours(2)->subMinutes(5),
            'settled_at' => null,
            'result' => null,
            'team_a_name' => 'Alpha Team',
            'team_b_name' => 'Beta Team',
            'home_team' => 'Alpha Team',
            'away_team' => 'Beta Team',
        ]);

        $market = MatchMarket::factory()->create([
            'match_id' => $match->id,
            'key' => MatchMarket::KEY_WINNER,
        ]);

        MatchSelection::factory()->create([
            'market_id' => $market->id,
            'key' => MatchSelection::KEY_TEAM_A,
            'label' => 'Alpha Team',
            'odds' => 2.000,
        ]);

        MatchSelection::factory()->create([
            'market_id' => $market->id,
            'key' => MatchSelection::KEY_TEAM_B,
            'label' => 'Beta Team',
            'odds' => 2.000,
        ]);

        $response = $this->actingAs($user)->get(route('matches.show', $match->id));

        $response->assertOk();
        $this->assertMatchesRegularExpression(
            '/name="selection_key"[^>]*value="'.MatchSelection::KEY_TEAM_A.'"[^>]*checked/s',
            $response->getContent()
        );
    }

    public function test_user_can_browse_matches_and_place_then_cancel_bet(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $match = EsportMatch::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
            'status' => EsportMatch::STATUS_SCHEDULED,
            'starts_at' => now()->addHours(2),
            'locked_at' => now()->addHours(2)->subMinutes(5),
            'settled_at' => null,
            'result' => null,
            'team_a_name' => 'Alpha Team',
            'team_b_name' => 'Beta Team',
            'home_team' => 'Alpha Team',
            'away_team' => 'Beta Team',
        ]);

        $market = MatchMarket::factory()->create([
            'match_id' => $match->id,
            'key' => MatchMarket::KEY_WINNER,
        ]);

        MatchSelection::factory()->create([
            'market_id' => $market->id,
            'key' => MatchSelection::KEY_TEAM_A,
            'label' => 'Alpha Team',
            'odds' => 2.000,
        ]);

        MatchSelection::factory()->create([
            'market_id' => $market->id,
            'key' => MatchSelection::KEY_TEAM_B,
            'label' => 'Beta Team',
            'odds' => 2.000,
        ]);

        $this->actingAs($user)
            ->get(route('matches.index'))
            ->assertOk()
            ->assertSee('Alpha Team');

        $this->actingAs($user)
            ->get(route('matches.show', $match->id))
            ->assertOk()
            ->assertSee('Parier sur le vainqueur');

        $place = $this->actingAs($user)->post(route('matches.bets.store', $match->id), [
            'selection_key' => MatchSelection::KEY_TEAM_A,
            'stake_points' => 75,
            'idempotency_key' => 'web-test-bet-place-001',
        ]);

        $place->assertRedirect();
        $place->assertSessionHas('success');

        $this->assertDatabaseHas('bets', [
            'user_id' => $user->id,
            'match_id' => $match->id,
            'status' => Bet::STATUS_PENDING,
            'stake_points' => 75,
        ]);

        $bet = Bet::query()->where('user_id', $user->id)->where('match_id', $match->id)->firstOrFail();

        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->id,
            'type' => WalletTransaction::TYPE_STAKE,
            'ref_type' => WalletTransaction::REF_TYPE_BET,
            'ref_id' => (string) $bet->id,
        ]);

        $cancel = $this->actingAs($user)->delete(route('bets.cancel', $bet->id), [
            'idempotency_key' => 'web-test-bet-cancel-001',
        ]);

        $cancel->assertRedirect();
        $cancel->assertSessionHas('success');

        $this->assertDatabaseHas('bets', [
            'id' => $bet->id,
            'status' => Bet::STATUS_CANCELLED,
        ]);

        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->id,
            'type' => WalletTransaction::TYPE_REFUND,
            'ref_type' => WalletTransaction::REF_TYPE_BET,
            'ref_id' => (string) $bet->id,
        ]);

        $this->actingAs($user)->get(route('bets.index'))->assertOk();
        $this->actingAs($user)->get(route('wallet.index'))->assertOk();
    }

    public function test_user_cannot_cancel_bet_after_one_hour_window(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $match = EsportMatch::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
            'status' => EsportMatch::STATUS_SCHEDULED,
            'starts_at' => now()->addHours(5),
            'locked_at' => now()->addHours(5)->subMinutes(5),
            'settled_at' => null,
            'result' => null,
        ]);

        $market = MatchMarket::factory()->create([
            'match_id' => $match->id,
            'key' => MatchMarket::KEY_WINNER,
        ]);

        MatchSelection::factory()->create([
            'market_id' => $market->id,
            'key' => MatchSelection::KEY_TEAM_A,
            'label' => 'Team A',
            'odds' => 2.000,
        ]);

        $this->actingAs($user)->post(route('matches.bets.store', $match->id), [
            'selection_key' => MatchSelection::KEY_TEAM_A,
            'stake_points' => 50,
            'idempotency_key' => 'web-test-bet-expire-place-001',
        ])->assertRedirect();

        $bet = Bet::query()->where('user_id', $user->id)->where('match_id', $match->id)->firstOrFail();

        $this->travel(((int) config('betting.cancellation.window_minutes', 60)) + 1)->minutes();

        $cancel = $this->actingAs($user)->delete(route('bets.cancel', $bet->id), [
            'idempotency_key' => 'web-test-bet-expire-cancel-001',
        ]);

        $cancel->assertRedirect();
        $cancel->assertSessionHas('error');

        $this->assertDatabaseHas('bets', [
            'id' => $bet->id,
            'status' => Bet::STATUS_PENDING,
        ]);

        $this->assertDatabaseMissing('wallet_transactions', [
            'user_id' => $user->id,
            'type' => WalletTransaction::TYPE_REFUND,
            'ref_type' => WalletTransaction::REF_TYPE_BET,
            'ref_id' => (string) $bet->id,
        ]);
    }

    public function test_admin_can_manage_match_and_grant_wallet(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $targetUser = User::factory()->create(['role' => User::ROLE_USER]);

        $create = $this->actingAs($admin)->post(route('admin.matches.store'), [
            'team_a_name' => 'Orion Squad',
            'team_b_name' => 'Nebula Unit',
            'starts_at' => now()->addHours(3)->toDateTimeString(),
            'locked_at' => now()->addHours(3)->subMinutes(5)->toDateTimeString(),
            'game_key' => 'valorant',
        ]);

        $create->assertRedirect();
        $create->assertSessionHas('success');

        $match = EsportMatch::query()->latest('id')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.matches.status', $match->id), ['status' => EsportMatch::STATUS_LOCKED])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->actingAs($admin)
            ->post(route('admin.matches.result', $match->id), ['result' => EsportMatch::RESULT_TEAM_A])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->actingAs($admin)
            ->post(route('admin.matches.settle', $match->id), [
                'result' => EsportMatch::RESULT_TEAM_A,
                'idempotency_key' => 'web-admin-settle-001',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $grant = $this->actingAs($admin)->post(route('admin.wallets.grant.store'), [
            'user_id' => $targetUser->id,
            'amount' => 350,
            'reason' => 'test grant',
            'idempotency_key' => 'web-wallet-grant-001',
        ]);

        $grant->assertRedirect();
        $grant->assertSessionHas('success');

        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $targetUser->id,
            'type' => WalletTransaction::TYPE_GRANT,
            'ref_type' => WalletTransaction::REF_TYPE_ADMIN,
            'ref_id' => (string) $admin->id,
        ]);

        $this->actingAs($admin)->get(route('admin.wallets.grant.create'))->assertOk();
        $this->actingAs($admin)->get(route('admin.matches.manage', $match->id))->assertOk();
    }

    public function test_admin_can_open_matches_index_with_tournament_and_child_matches(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $tournament = EsportMatch::factory()->rocketLeagueTournament()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
            'event_name' => 'RLCS Open Europe #2',
            'competition_name' => 'RLCS Europe',
            'child_matches_unlocked_at' => now()->subHour(),
        ]);

        EsportMatch::factory()->rocketLeagueChildMatch($tournament)->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
            'team_a_name' => 'ERAH Rocket League',
            'team_b_name' => 'North Star',
            'home_team' => 'ERAH Rocket League',
            'away_team' => 'North Star',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.matches.index'))
            ->assertOk()
            ->assertSee('RLCS Open Europe #2')
            ->assertSee('ERAH Rocket League');
    }
}
