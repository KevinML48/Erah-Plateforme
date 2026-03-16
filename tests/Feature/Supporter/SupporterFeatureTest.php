<?php

namespace Tests\Feature\Supporter;

use App\Application\Actions\Ranking\AddPointsAction;
use App\Application\Actions\Rewards\EnsureCurrentMissionInstancesAction;
use App\Models\Clip;
use App\Models\ClipComment;
use App\Models\ClipVoteCampaign;
use App\Models\ClipVoteEntry;
use App\Models\League;
use App\Models\MissionTemplate;
use App\Models\PointsTransaction;
use App\Models\SupporterPlan;
use App\Models\User;
use App\Models\UserProgress;
use App\Models\UserSupportSubscription;
use App\Services\GrantMonthlySupporterRewards;
use App\Services\PrioritizeClipComments;
use Database\Seeders\LeagueSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupporterFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_stripe_webhook_creates_cashier_and_custom_support_subscription(): void
    {
        $user = User::factory()->create(['stripe_id' => 'cus_supporter_1']);

        $response = $this->postJson(route('stripe.webhook'), [
            'type' => 'customer.subscription.created',
            'data' => [
                'object' => [
                    'id' => 'sub_supporter_1',
                    'customer' => 'cus_supporter_1',
                    'status' => 'active',
                    'start_date' => now()->subDay()->timestamp,
                    'current_period_start' => now()->startOfMonth()->timestamp,
                    'current_period_end' => now()->endOfMonth()->timestamp,
                    'items' => [
                        'data' => [[
                            'id' => 'si_supporter_1',
                            'quantity' => 1,
                            'price' => [
                                'id' => 'price_supporter_1',
                                'product' => 'prod_supporter_1',
                            ],
                        ]],
                    ],
                    'metadata' => [
                        'type' => config('supporter.plan.subscription_type'),
                    ],
                ],
            ],
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'type' => config('supporter.plan.subscription_type'),
            'stripe_id' => 'sub_supporter_1',
            'stripe_status' => 'active',
        ]);

        $subscription = UserSupportSubscription::query()->where('user_id', $user->id)->first();
        $this->assertNotNull($subscription);
        $this->assertSame(UserSupportSubscription::STATUS_ACTIVE, $subscription->status);
        $this->assertSame('sub_supporter_1', $subscription->provider_subscription_id);
        $this->assertNotNull($subscription->current_period_end);
    }

    public function test_supporter_only_clip_reaction_requires_active_supporter(): void
    {
        $clip = Clip::factory()->create();
        /** @var User $regularUser */
        $regularUser = User::factory()->create();
        /** @var User $supporter */
        $supporter = User::factory()->create();
        $this->activateSupport($supporter);

        $this->actingAs($regularUser)
            ->post(route('clips.supporter-reactions.store', $clip->id), ['reaction_key' => 'fire'])
            ->assertForbidden();

        $this->actingAs($supporter)
            ->post(route('clips.supporter-reactions.store', $clip->id), ['reaction_key' => 'fire'])
            ->assertRedirect();

        $this->assertDatabaseHas('clip_supporter_reactions', [
            'clip_id' => $clip->id,
            'user_id' => $supporter->id,
            'reaction_key' => 'fire',
        ]);
    }

    public function test_supporter_vote_is_unique_per_campaign_and_user(): void
    {
        /** @var User $supporter */
        $supporter = User::factory()->create();
        $this->activateSupport($supporter);

        $clipA = Clip::factory()->create();
        $clipB = Clip::factory()->create();

        $campaign = ClipVoteCampaign::query()->create([
            'type' => ClipVoteCampaign::TYPE_WEEKLY,
            'title' => 'Clip de la semaine',
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addDay(),
            'status' => ClipVoteCampaign::STATUS_ACTIVE,
        ]);

        ClipVoteEntry::query()->create(['campaign_id' => $campaign->id, 'clip_id' => $clipA->id]);
        ClipVoteEntry::query()->create(['campaign_id' => $campaign->id, 'clip_id' => $clipB->id]);

        $this->actingAs($supporter)
            ->post(route('clips.campaigns.vote', $campaign->id), ['clip_id' => $clipA->id])
            ->assertRedirect();

        $this->actingAs($supporter)
            ->post(route('clips.campaigns.vote', $campaign->id), ['clip_id' => $clipB->id])
            ->assertRedirect();

        $this->assertDatabaseCount('clip_votes', 1);
        $this->assertDatabaseHas('clip_votes', [
            'campaign_id' => $campaign->id,
            'user_id' => $supporter->id,
            'clip_id' => $clipB->id,
        ]);
    }

    public function test_supporter_only_missions_are_not_generated_for_non_supporters(): void
    {
        $this->seed(LeagueSeeder::class);

        /** @var User $regularUser */
        $regularUser = User::factory()->create();
        /** @var User $supporter */
        $supporter = User::factory()->create();
        $this->activateSupport($supporter);

        MissionTemplate::query()->create([
            'key' => 'supporter-monthly-test',
            'title' => 'Mission supporter test',
            'description' => 'Mission reservee aux supporters.',
            'event_type' => 'supporter_monthly_test',
            'target_count' => 1,
            'scope' => MissionTemplate::SCOPE_MONTHLY,
            'constraints' => ['supporter_only' => true],
            'rewards' => ['xp' => 20, 'rank_points' => 5, 'reward_points' => 10, 'bet_points' => 0],
            'is_active' => true,
        ]);

        app(EnsureCurrentMissionInstancesAction::class)->execute($regularUser);
        app(EnsureCurrentMissionInstancesAction::class)->execute($supporter);

        $this->assertSame(0, $regularUser->missionProgress()->count());
        $this->assertSame(1, $supporter->missionProgress()->count());
    }

    public function test_supporter_xp_bonus_is_applied_to_xp_transactions(): void
    {
        $this->seed(LeagueSeeder::class);

        /** @var User $user */
        $user = User::factory()->create();
        $this->activateSupport($user);

        $result = app(AddPointsAction::class)->execute(
            $user,
            PointsTransaction::KIND_XP,
            100,
            'test.supporter_xp',
            'source-1'
        );

        $this->assertFalse($result->idempotent);
        $this->assertSame(115, $result->transaction->points);
        $this->assertSame(115, $result->progress->total_xp);
    }

    public function test_supporter_comments_are_prioritized_in_clip_feed(): void
    {
        $clip = Clip::factory()->create();
        /** @var User $regularUser */
        $regularUser = User::factory()->create();
        /** @var User $supporter */
        $supporter = User::factory()->create();
        $this->activateSupport($supporter);

        ClipComment::query()->create([
            'clip_id' => $clip->id,
            'user_id' => $regularUser->id,
            'body' => 'Commentaire standard',
        ]);

        ClipComment::query()->create([
            'clip_id' => $clip->id,
            'user_id' => $supporter->id,
            'body' => 'Commentaire supporter',
        ]);

        $comments = app(PrioritizeClipComments::class)->execute($clip, 10);

        $this->assertSame($supporter->id, $comments->items()[0]->user_id);
    }

    public function test_leaderboard_page_displays_supporter_badge(): void
    {
        $this->seed(LeagueSeeder::class);

        $league = League::query()->where('key', 'bronze')->firstOrFail();
        /** @var User $supporter */
        $supporter = User::factory()->create();
        $this->activateSupport($supporter);

        UserProgress::query()->create([
            'user_id' => $supporter->id,
            'current_league_id' => $league->id,
            'total_rank_points' => 80,
            'total_xp' => 200,
            'last_points_at' => now(),
        ]);

        $response = $this->actingAs($supporter)->get(route('leaderboards.show', $league->key));

        $response->assertOk();
        $response->assertSee('Supporter');
    }

    public function test_leaderboard_page_displays_provider_avatar_when_no_uploaded_avatar_exists(): void
    {
        $this->seed(LeagueSeeder::class);

        $league = League::query()->where('key', 'bronze')->firstOrFail();
        /** @var User $member */
        $member = User::factory()->create([
            'provider_avatar_url' => 'https://cdn.example.com/leaderboard-provider-avatar.png',
            'provider_avatar_provider' => 'google',
        ]);

        UserProgress::query()->create([
            'user_id' => $member->id,
            'current_league_id' => $league->id,
            'total_rank_points' => 60,
            'total_xp' => 180,
            'last_points_at' => now(),
        ]);

        $this->actingAs($member)
            ->get(route('leaderboards.show', $league->key))
            ->assertOk()
            ->assertSee('https://cdn.example.com/leaderboard-provider-avatar.png', false);
    }

    public function test_monthly_supporter_rewards_are_granted_once_per_month(): void
    {
        $this->seed(LeagueSeeder::class);

        /** @var User $user */
        $user = User::factory()->create();
        $this->activateSupport($user);
        $month = now()->startOfMonth();

        app(GrantMonthlySupporterRewards::class)->execute($month);
        app(GrantMonthlySupporterRewards::class)->execute($month);

        $this->assertSame(
            1,
            $user->supporterMonthlyRewards()
                ->where('reward_key', 'monthly_progress')
                ->whereDate('reward_month', $month->toDateString())
                ->count()
        );

        $this->assertSame(1, $user->notifications()->where('title', 'Avantages supporter du mois')->count());
        $this->assertSame(
            1,
            $user->supporterMonthlyRewards()
                ->where('reward_key', 'monthly_progress')
                ->whereDate('reward_month', $month->toDateString())
                ->count()
        );
    }

    private function activateSupport(User $user): UserSupportSubscription
    {
        $plan = SupporterPlan::query()->firstOrCreate([
            'key' => config('supporter.plan.key'),
        ], [
            'name' => config('supporter.plan.name'),
            'price_cents' => 499,
            'currency' => 'eur',
            'billing_interval' => 'month',
            'description' => 'Supporter ERAH',
            'is_active' => true,
        ]);

        return UserSupportSubscription::query()->create([
            'user_id' => $user->id,
            'supporter_plan_id' => $plan->id,
            'status' => UserSupportSubscription::STATUS_ACTIVE,
            'provider' => 'stripe',
            'provider_customer_id' => 'cus_'.$user->id,
            'provider_subscription_id' => 'sub_'.$user->id,
            'provider_price_id' => 'price_supporter',
            'started_at' => now()->subMonth(),
            'current_period_start' => now()->startOfMonth(),
            'current_period_end' => now()->endOfMonth(),
            'meta' => ['source' => 'test'],
        ]);
    }
}
