<?php

namespace Tests\Feature\Community;

use App\Models\Duel;
use App\Models\MissionTemplate;
use App\Models\LiveCode;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\User;
use App\Models\UserMission;
use App\Models\UserProgress;
use App\Services\BetService;
use App\Application\Actions\Rewards\EnsureCurrentMissionInstancesAction;
use App\Models\Bet;
use App\Models\CommunityRewardGrant;
use Database\Seeders\CommunityPlatformSeeder;
use Database\Seeders\LeagueSeeder;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CommunityPlatformFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_pass_quiz_and_receive_rewards(): void
    {
        $user = User::factory()->create();
        $quiz = Quiz::query()->create([
            'title' => 'Quiz ERAH',
            'slug' => 'quiz-erah',
            'description' => 'Quiz test',
            'intro' => 'Intro',
            'pass_score' => 1,
            'max_attempts_per_user' => 2,
            'reward_points' => 60,
            'xp_reward' => 90,
            'is_active' => true,
        ]);

        $question = $quiz->questions()->create([
            'prompt' => 'Quelle ressource sert a progresser ?',
            'sort_order' => 1,
            'points' => 1,
            'is_active' => true,
        ]);

        $wrong = $question->answers()->create([
            'label' => 'Les bet points',
            'is_correct' => false,
            'sort_order' => 1,
        ]);

        $correct = $question->answers()->create([
            'label' => 'L XP',
            'is_correct' => true,
            'sort_order' => 2,
        ]);

        $response = $this->actingAs($user)->post(route('quizzes.attempt', ['slug' => $quiz->slug]), [
            'answers' => [
                $question->id => $correct->id,
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $attempt = QuizAttempt::query()->firstOrFail();
        $this->assertTrue($attempt->passed);
        $this->assertSame(1, $attempt->score);
        $this->assertNotNull($attempt->reward_granted_at);

        $this->assertDatabaseHas('community_reward_grants', [
            'user_id' => $user->id,
            'domain' => 'quiz',
            'action' => 'pass',
            'xp_amount' => 90,
            'reward_points_amount' => 60,
        ]);

        $this->assertDatabaseHas('user_reward_wallets', [
            'user_id' => $user->id,
            'balance' => 60,
        ]);

        $this->assertDatabaseHas('user_progress', [
            'user_id' => $user->id,
            'total_xp' => 90,
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'category' => 'quiz',
        ]);

        $this->assertNotNull($wrong->id);
    }

    public function test_user_can_pass_short_text_quiz(): void
    {
        $user = User::factory()->create();
        $quiz = Quiz::query()->create([
            'title' => 'Culture ERAH',
            'slug' => 'culture-erah',
            'description' => 'Quiz reponse courte',
            'pass_score' => 1,
            'reward_points' => 20,
            'xp_reward' => 30,
            'is_active' => true,
        ]);

        $quiz->questions()->create([
            'prompt' => 'Quel est le nom du club ?',
            'question_type' => QuizQuestion::TYPE_SHORT_TEXT,
            'accepted_answer' => 'ERAH',
            'sort_order' => 1,
            'points' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('quizzes.attempt', ['slug' => $quiz->slug]), [
            'answers' => [
                $quiz->questions()->value('id') => ' erah ',
            ],
        ]);

        $response->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('quiz_attempts', [
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'passed' => true,
            'score' => 1,
        ]);
    }

    public function test_live_code_redemption_is_limited_per_user_and_rewards_user(): void
    {
        $user = User::factory()->create();
        $liveCode = LiveCode::query()->create([
            'code' => 'ERAHLIVE',
            'label' => 'Code bonus',
            'description' => 'Bonus test',
            'status' => 'published',
            'reward_points' => 75,
            'bet_points' => 25,
            'xp_reward' => 50,
            'usage_limit' => 3,
            'per_user_limit' => 1,
            'expires_at' => now()->addDay(),
        ]);

        $first = $this->actingAs($user)->post(route('live-codes.redeem'), [
            'code' => 'erahlive',
        ]);

        $first->assertRedirect();
        $first->assertSessionHas('success');

        $this->assertDatabaseHas('live_code_redemptions', [
            'live_code_id' => $liveCode->id,
            'user_id' => $user->id,
            'reward_points' => 75,
            'bet_points' => 25,
            'xp_reward' => 50,
        ]);

        $this->assertDatabaseHas('user_reward_wallets', [
            'user_id' => $user->id,
            'balance' => 100,
        ]);

        $this->assertDatabaseHas('user_wallets', [
            'user_id' => $user->id,
            'balance' => 100,
        ]);

        $second = $this->actingAs($user)->post(route('live-codes.redeem'), [
            'code' => 'ERAHLIVE',
        ]);

        $second->assertRedirect();
        $second->assertSessionHas('error', 'Code deja utilise.');

        $this->assertDatabaseCount('live_code_redemptions', 1);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'category' => 'live_code',
        ]);
    }

    public function test_admin_can_link_a_live_code_to_a_mission_and_redemption_progresses_that_mission(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create();

        $template = MissionTemplate::query()->create([
            'key' => 'mission.live.linked-code',
            'title' => 'Mission code live ciblee',
            'short_description' => 'Valider la mission avec un code du direct.',
            'description' => 'Valider la mission avec un code du direct.',
            'event_type' => 'clip.view',
            'target_count' => 1,
            'scope' => MissionTemplate::SCOPE_ONCE,
            'category' => 'live',
            'type' => 'event',
            'rewards' => ['xp' => 120, 'points' => 80],
            'is_active' => true,
        ]);

        app(EnsureCurrentMissionInstancesAction::class)->execute($user);

        $storeResponse = $this->actingAs($admin)->post(route('admin.live-codes.store'), [
            'code' => 'MISSIONLIVE',
            'label' => 'Code mission live',
            'description' => 'Code du stream pour valider la mission.',
            'status' => 'published',
            'reward_points' => 30,
            'bet_points' => 0,
            'xp_reward' => 20,
            'per_user_limit' => 1,
            'mission_template_id' => $template->id,
            'expires_at' => now()->addHour()->toDateTimeString(),
        ]);

        $storeResponse->assertRedirect();

        $liveCode = LiveCode::query()->where('code', 'MISSIONLIVE')->firstOrFail();
        $this->assertSame($template->id, (int) $liveCode->mission_template_id);

        $redeemResponse = $this->actingAs($user)->post(route('live-codes.redeem'), [
            'code' => 'MISSIONLIVE',
        ]);

        $redeemResponse->assertRedirect();
        $redeemResponse->assertSessionHas('success');

        $mission = UserMission::query()
            ->where('user_id', $user->id)
            ->whereHas('instance', fn ($query) => $query->where('mission_template_id', $template->id))
            ->firstOrFail();

        $this->assertSame(1, (int) $mission->progress_count);
        $this->assertNotNull($mission->completed_at);
    }

    public function test_admin_can_record_duel_result_and_apply_rewards(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $winner = User::factory()->create();
        $loser = User::factory()->create();

        $duel = Duel::factory()->create([
            'challenger_id' => $winner->id,
            'challenged_id' => $loser->id,
            'status' => Duel::STATUS_ACCEPTED,
            'responded_at' => now(),
            'accepted_at' => now(),
        ]);

        $response = $this->actingAs($admin)->post(route('admin.duels.result.store', ['duelId' => $duel->id]), [
            'winner_user_id' => $winner->id,
            'challenger_score' => 3,
            'challenged_score' => 1,
            'note' => 'Serie propre',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('duel_results', [
            'duel_id' => $duel->id,
            'winner_user_id' => $winner->id,
            'loser_user_id' => $loser->id,
        ]);

        $this->assertDatabaseHas('duels', [
            'id' => $duel->id,
            'status' => Duel::STATUS_SETTLED,
        ]);

        $this->assertDatabaseHas('user_progress', [
            'user_id' => $winner->id,
            'duel_score' => 25,
            'duel_wins' => 1,
            'duel_current_streak' => 1,
            'duel_best_streak' => 1,
        ]);

        $this->assertDatabaseHas('user_progress', [
            'user_id' => $loser->id,
            'duel_score' => -10,
            'duel_losses' => 1,
        ]);

        $this->assertDatabaseHas('user_reward_wallets', [
            'user_id' => $winner->id,
            'balance' => 150,
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $winner->id,
            'category' => 'duel',
        ]);
    }

    public function test_duel_rewards_are_blocked_after_repeated_farming_against_same_opponent(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $winner = User::factory()->create();
        $loser = User::factory()->create();

        foreach (range(1, 4) as $index) {
            $duel = Duel::factory()->create([
                'challenger_id' => $winner->id,
                'challenged_id' => $loser->id,
                'status' => Duel::STATUS_ACCEPTED,
                'responded_at' => now(),
                'accepted_at' => now(),
            ]);

            $this->actingAs($admin)->post(route('admin.duels.result.store', ['duelId' => $duel->id]), [
                'winner_user_id' => $winner->id,
                'challenger_score' => 3,
                'challenged_score' => 0,
            ])->assertRedirect();
        }

        $this->assertDatabaseHas('user_progress', [
            'user_id' => $winner->id,
            'duel_wins' => 3,
            'duel_score' => 75,
            'duel_current_streak' => 3,
            'duel_best_streak' => 3,
        ]);

        $this->assertSame(3, CommunityRewardGrant::query()->where('domain', 'duels')->where('action', 'win')->count());
        $this->assertSame(3, CommunityRewardGrant::query()->where('domain', 'duels')->where('action', 'loss')->count());
    }

    public function test_community_leaderboard_api_and_push_subscription_endpoints_work(): void
    {
        $this->seed(LeagueSeeder::class);

        $first = User::factory()->create(['name' => 'Alpha']);
        $second = User::factory()->create(['name' => 'Bravo']);

        UserProgress::query()->create([
            'user_id' => $first->id,
            'current_league_id' => null,
            'total_xp' => 3200,
            'total_rank_points' => 400,
            'duel_score' => 40,
            'duel_wins' => 3,
            'duel_losses' => 1,
            'duel_current_streak' => 2,
            'duel_best_streak' => 4,
        ]);

        UserProgress::query()->create([
            'user_id' => $second->id,
            'current_league_id' => null,
            'total_xp' => 1800,
            'total_rank_points' => 250,
            'duel_score' => 15,
            'duel_wins' => 2,
            'duel_losses' => 2,
            'duel_current_streak' => 1,
            'duel_best_streak' => 2,
        ]);

        $leaderboards = $this->getJson('/api/community/leaderboards?limit=5');
        $leaderboards->assertOk()
            ->assertJsonPath('xp.0.user_id', $first->id)
            ->assertJsonPath('rank.0.user_id', $first->id)
            ->assertJsonPath('duel.0.user_id', $first->id);

        Sanctum::actingAs($first);

        $store = $this->postJson('/api/me/push-subscriptions', [
            'endpoint' => 'https://push.example.test/subscriptions/1',
            'public_key' => 'public-key',
            'auth_token' => 'auth-token',
            'content_encoding' => 'aes128gcm',
            'categories' => ['duel', 'quiz'],
        ]);

        $store->assertCreated()
            ->assertJsonPath('data.is_active', true);

        $this->assertDatabaseHas('push_subscriptions', [
            'user_id' => $first->id,
            'is_active' => true,
        ]);

        $delete = $this->deleteJson('/api/me/push-subscriptions', [
            'endpoint' => 'https://push.example.test/subscriptions/1',
        ]);

        $delete->assertOk();

        $this->assertDatabaseHas('push_subscriptions', [
            'user_id' => $first->id,
            'is_active' => false,
        ]);
    }

    public function test_login_streak_is_created_on_login_event(): void
    {
        $user = User::factory()->create();

        event(new Login('web', $user, false));
        event(new Login('web', $user, false));

        $this->assertDatabaseHas('user_login_streaks', [
            'user_id' => $user->id,
            'current_streak' => 1,
            'longest_streak' => 1,
            'last_reward_points' => 20,
        ]);

        $this->assertDatabaseHas('user_reward_wallets', [
            'user_id' => $user->id,
            'balance' => 20,
        ]);
    }

    public function test_daily_missions_are_selected_with_expected_simple_medium_special_mix(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 4) as $index) {
            MissionTemplate::query()->create([
                'key' => 'mission.daily.simple.'.$index,
                'title' => 'Simple '.$index,
                'event_type' => 'clip.view',
                'target_count' => 1,
                'scope' => MissionTemplate::SCOPE_DAILY,
                'constraints' => ['difficulty' => 'simple'],
                'rewards' => ['xp' => 10, 'points' => 5],
                'is_active' => true,
            ]);
        }

        MissionTemplate::query()->create([
            'key' => 'mission.daily.medium.1',
            'title' => 'Medium 1',
            'event_type' => 'clip.like',
            'target_count' => 2,
            'scope' => MissionTemplate::SCOPE_DAILY,
            'constraints' => ['difficulty' => 'medium'],
            'rewards' => ['xp' => 20, 'points' => 10],
            'is_active' => true,
        ]);

        MissionTemplate::query()->create([
            'key' => 'mission.daily.special.1',
            'title' => 'Special 1',
            'event_type' => 'duel.win',
            'target_count' => 1,
            'scope' => MissionTemplate::SCOPE_DAILY,
            'constraints' => ['difficulty' => 'special'],
            'rewards' => ['xp' => 30, 'points' => 15],
            'is_active' => true,
        ]);

        $result = app(EnsureCurrentMissionInstancesAction::class)->execute($user);

        $this->assertSame(5, $result['daily']);
        $this->assertSame(5, $user->missionProgress()->count());
    }

    public function test_bet_rewards_stop_granting_xp_after_daily_limit(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 21) as $index) {
            $bet = Bet::factory()->create([
                'user_id' => $user->id,
                'market_key' => 'winner',
                'selection_key' => 'team_a',
                'stake' => 100,
                'odds_snapshot' => 1.5,
                'prediction' => Bet::PREDICTION_HOME,
                'stake_points' => 100,
                'potential_payout' => 125,
                'settlement_points' => 0,
                'status' => Bet::STATUS_WON,
                'idempotency_key' => 'bet-'.$index,
                'placed_at' => now(),
                'settled_at' => now(),
                'payout' => 125,
            ]);

            app(BetService::class)->rewardSettlement($bet->fresh('user'));
        }

        $this->assertSame(20, CommunityRewardGrant::query()->where('domain', 'bets')->where('action', 'win')->count());
        $this->assertDatabaseHas('user_progress', [
            'user_id' => $user->id,
            'total_xp' => 1200,
        ]);
    }

    public function test_statistics_and_duel_leaderboard_pages_are_accessible(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('statistics.index'))
            ->assertOk()
            ->assertSee('Statistiques');

        $this->actingAs($user)
            ->get(route('duels.leaderboard'))
            ->assertOk()
            ->assertSee('Classement duel');
    }

    public function test_community_platform_seeder_inserts_default_content(): void
    {
        $this->seed(CommunityPlatformSeeder::class);

        $this->assertDatabaseHas('achievements', [
            'key' => 'clips.first_view',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('shop_items', [
            'key' => 'badge.community-founder',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('quizzes', [
            'slug' => 'quiz-erah-communaute',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('live_codes', [
            'code' => 'ERAHLIVE',
            'status' => 'published',
        ]);

        $this->assertDatabaseHas('events', [
            'key' => 'bonus-clips-launch',
            'status' => 'published',
        ]);
    }
}
