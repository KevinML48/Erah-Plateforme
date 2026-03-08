<?php

namespace Tests\Feature\Matches;

use App\Application\Actions\Matches\SyncMatchMarketsAction;
use App\Models\Bet;
use App\Models\EsportMatch;
use App\Models\MatchMarket;
use App\Models\MatchSelection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RocketLeagueTournamentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_rocket_league_tournament_run_event(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->post(route('admin.matches.store'), [
            'event_type' => EsportMatch::EVENT_TYPE_TOURNAMENT_RUN,
            'game_key' => EsportMatch::GAME_ROCKET_LEAGUE,
            'event_name' => 'RLCS Open Europe #1',
            'competition_name' => 'RLCS Europe',
            'competition_stage' => 'Open Qualifier',
            'competition_split' => 'Spring Split',
            'starts_at' => now()->addDay()->toDateTimeString(),
            'locked_at' => now()->addDay()->subHour()->toDateTimeString(),
            'ends_at' => now()->addDays(3)->toDateTimeString(),
            'market_preset' => 'rocket_league_tournament',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $tournament = EsportMatch::query()->latest('id')->firstOrFail();

        $this->assertSame(EsportMatch::EVENT_TYPE_TOURNAMENT_RUN, $tournament->event_type);
        $this->assertSame(EsportMatch::GAME_ROCKET_LEAGUE, $tournament->game_key);
        $this->assertSame('RLCS Open Europe #1', $tournament->event_name);
        $this->assertDatabaseHas('match_markets', [
            'match_id' => $tournament->id,
            'key' => MatchMarket::KEY_TOURNAMENT_FINISH,
        ]);
        $this->assertDatabaseHas('match_selections', [
            'key' => MatchSelection::KEY_TOP_16,
        ]);
    }

    public function test_user_can_place_tournament_run_prediction_on_rocket_league_event(): void
    {
        $user = User::factory()->create();
        $tournament = $this->createRocketLeagueTournament();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/bets', [
            'match_id' => $tournament->id,
            'market_key' => MatchMarket::KEY_TOURNAMENT_FINISH,
            'selection_key' => MatchSelection::KEY_TOP_8,
            'stake_points' => 120,
            'idempotency_key' => 'rl-tournament-bet-001',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.market_key', MatchMarket::KEY_TOURNAMENT_FINISH)
            ->assertJsonPath('data.selection_key', MatchSelection::KEY_TOP_8);

        $this->assertDatabaseHas('bets', [
            'user_id' => $user->id,
            'match_id' => $tournament->id,
            'market_key' => MatchMarket::KEY_TOURNAMENT_FINISH,
            'selection_key' => MatchSelection::KEY_TOP_8,
            'status' => Bet::STATUS_PENDING,
        ]);
    }

    public function test_user_cannot_place_child_match_prediction_before_parent_unlock(): void
    {
        $user = User::factory()->create();
        $parentTournament = $this->createRocketLeagueTournament(unlocked: false);
        $childMatch = $this->createRocketLeagueChildMatch($parentTournament);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/bets', [
            'match_id' => $childMatch->id,
            'market_key' => MatchMarket::KEY_WINNER,
            'selection_key' => MatchSelection::KEY_TEAM_A,
            'stake_points' => 75,
            'idempotency_key' => 'rl-child-locked-001',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Tournament match phase is not unlocked yet.');
    }

    public function test_admin_can_create_rocket_league_child_match_linked_to_tournament_parent(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $parentTournament = $this->createRocketLeagueTournament(unlocked: true);

        $response = $this->actingAs($admin)->post(route('admin.matches.store'), [
            'event_type' => EsportMatch::EVENT_TYPE_HEAD_TO_HEAD,
            'game_key' => EsportMatch::GAME_ROCKET_LEAGUE,
            'parent_match_id' => $parentTournament->id,
            'team_a_name' => 'ERAH Rocket League',
            'team_b_name' => 'Pulse Engine',
            'competition_name' => $parentTournament->competition_name,
            'competition_stage' => 'Top 16',
            'competition_split' => $parentTournament->competition_split,
            'best_of' => 5,
            'starts_at' => now()->addDays(2)->toDateTimeString(),
            'locked_at' => now()->addDays(2)->subMinutes(5)->toDateTimeString(),
            'market_preset' => 'rocket_league_bo5',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $childMatch = EsportMatch::query()->latest('id')->firstOrFail();

        $this->assertSame($parentTournament->id, $childMatch->parent_match_id);
        $this->assertSame(EsportMatch::EVENT_TYPE_HEAD_TO_HEAD, $childMatch->event_type);
        $this->assertSame(5, $childMatch->best_of);
        $this->assertDatabaseHas('match_markets', [
            'match_id' => $childMatch->id,
            'key' => MatchMarket::KEY_EXACT_SCORE,
        ]);
    }

    public function test_admin_can_settle_tournament_run_prediction(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create();
        $tournament = $this->createRocketLeagueTournament();

        Sanctum::actingAs($user);
        $this->postJson('/api/bets', [
            'match_id' => $tournament->id,
            'market_key' => MatchMarket::KEY_TOURNAMENT_FINISH,
            'selection_key' => MatchSelection::KEY_TOP_8,
            'stake_points' => 100,
            'idempotency_key' => 'rl-tournament-settle-bet-001',
        ])->assertCreated();

        Sanctum::actingAs($admin);
        $response = $this->postJson('/api/admin/matches/'.$tournament->id.'/settle', [
            'result' => MatchSelection::KEY_TOP_8,
            'idempotency_key' => 'rl-tournament-settle-001',
        ]);

        $response->assertOk()
            ->assertJsonPath('idempotent', false)
            ->assertJsonPath('settlement.won_count', 1)
            ->assertJsonPath('match.result', MatchSelection::KEY_TOP_8);

        $this->assertDatabaseHas('bets', [
            'user_id' => $user->id,
            'match_id' => $tournament->id,
            'market_key' => MatchMarket::KEY_TOURNAMENT_FINISH,
            'selection_key' => MatchSelection::KEY_TOP_8,
            'status' => Bet::STATUS_WON,
        ]);
    }

    public function test_admin_can_settle_rocket_league_top_16_child_match_with_exact_score(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create();
        $parentTournament = $this->createRocketLeagueTournament(unlocked: true);
        $childMatch = $this->createRocketLeagueChildMatch($parentTournament, 5);

        Sanctum::actingAs($user);
        $this->postJson('/api/bets', [
            'match_id' => $childMatch->id,
            'market_key' => MatchMarket::KEY_WINNER,
            'selection_key' => MatchSelection::KEY_TEAM_A,
            'stake_points' => 80,
            'idempotency_key' => 'rl-child-winner-001',
        ])->assertCreated();

        $this->postJson('/api/bets', [
            'match_id' => $childMatch->id,
            'market_key' => MatchMarket::KEY_EXACT_SCORE,
            'selection_key' => '3_1',
            'stake_points' => 40,
            'idempotency_key' => 'rl-child-score-001',
        ])->assertCreated();

        Sanctum::actingAs($admin);
        $response = $this->postJson('/api/admin/matches/'.$childMatch->id.'/settle', [
            'result' => EsportMatch::RESULT_TEAM_A,
            'team_a_score' => 3,
            'team_b_score' => 1,
            'idempotency_key' => 'rl-child-settle-001',
        ]);

        $response->assertOk()
            ->assertJsonPath('settlement.won_count', 2)
            ->assertJsonPath('match.result', EsportMatch::RESULT_HOME)
            ->assertJsonPath('match.team_a_score', 3)
            ->assertJsonPath('match.team_b_score', 1);

        $this->assertDatabaseHas('bets', [
            'match_id' => $childMatch->id,
            'market_key' => MatchMarket::KEY_WINNER,
            'selection_key' => MatchSelection::KEY_TEAM_A,
            'status' => Bet::STATUS_WON,
        ]);
        $this->assertDatabaseHas('bets', [
            'match_id' => $childMatch->id,
            'market_key' => MatchMarket::KEY_EXACT_SCORE,
            'selection_key' => '3_1',
            'status' => Bet::STATUS_WON,
        ]);
    }

    public function test_classic_valorant_match_flow_remains_compatible_with_legacy_winner_prediction(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create();
        $match = EsportMatch::factory()->create([
            'game_key' => EsportMatch::GAME_VALORANT,
            'event_type' => EsportMatch::EVENT_TYPE_HEAD_TO_HEAD,
            'best_of' => 3,
            'team_a_name' => 'ERAH Valorant',
            'team_b_name' => 'Nova Unit',
            'home_team' => 'ERAH Valorant',
            'away_team' => 'Nova Unit',
            'starts_at' => now()->addHours(3),
            'locked_at' => now()->addHours(3)->subMinutes(5),
            'status' => EsportMatch::STATUS_SCHEDULED,
        ]);

        app(SyncMatchMarketsAction::class)->execute($match, null, $match->toArray());

        Sanctum::actingAs($user);
        $this->postJson('/api/bets', [
            'match_id' => $match->id,
            'prediction' => Bet::PREDICTION_HOME,
            'stake_points' => 60,
            'idempotency_key' => 'valorant-legacy-001',
        ])->assertCreated();

        Sanctum::actingAs($admin);
        $this->postJson('/api/admin/matches/'.$match->id.'/settle', [
            'result' => EsportMatch::RESULT_HOME,
            'idempotency_key' => 'valorant-settle-001',
        ])->assertOk();

        $this->assertDatabaseHas('bets', [
            'match_id' => $match->id,
            'market_key' => MatchMarket::KEY_WINNER,
            'selection_key' => MatchSelection::KEY_TEAM_A,
            'status' => Bet::STATUS_WON,
        ]);
    }

    private function createRocketLeagueTournament(bool $unlocked = false): EsportMatch
    {
        $tournament = EsportMatch::factory()->rocketLeagueTournament()->create([
            'status' => EsportMatch::STATUS_SCHEDULED,
            'starts_at' => now()->addDay(),
            'locked_at' => now()->addDay()->subHour(),
            'child_matches_unlocked_at' => $unlocked ? now()->subHour() : null,
        ]);

        app(SyncMatchMarketsAction::class)->execute($tournament, null, array_merge($tournament->toArray(), [
            'market_preset' => 'rocket_league_tournament',
        ]));

        return $tournament->fresh(['markets.selections']);
    }

    private function createRocketLeagueChildMatch(EsportMatch $parentTournament, int $bestOf = 5): EsportMatch
    {
        $childMatch = EsportMatch::factory()->rocketLeagueChildMatch($parentTournament)->create([
            'game_key' => EsportMatch::GAME_ROCKET_LEAGUE,
            'event_type' => EsportMatch::EVENT_TYPE_HEAD_TO_HEAD,
            'best_of' => $bestOf,
            'team_a_name' => 'ERAH Rocket League',
            'team_b_name' => 'North Star',
            'home_team' => 'ERAH Rocket League',
            'away_team' => 'North Star',
            'starts_at' => now()->addDays(2),
            'locked_at' => now()->addDays(2)->subMinutes(5),
            'status' => EsportMatch::STATUS_SCHEDULED,
        ]);

        app(SyncMatchMarketsAction::class)->execute($childMatch, null, array_merge($childMatch->toArray(), [
            'market_preset' => $bestOf === 7 ? 'rocket_league_bo7' : 'rocket_league_bo5',
        ]));

        return $childMatch->fresh(['markets.selections', 'parentMatch']);
    }
}
