<?php

namespace Tests\Feature\Missions;

use App\Application\Actions\Ranking\AddPointsAction;
use App\Application\Actions\Rewards\EnsureCurrentMissionInstancesAction;
use App\Domain\Notifications\Enums\NotificationCategory;
use App\Models\MissionCompletion;
use App\Models\MissionEventRecord;
use App\Models\MissionInstance;
use App\Models\MissionTemplate;
use App\Models\PointsTransaction;
use App\Models\UserLoginStreak;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserMission;
use App\Services\MissionCatalogService;
use App\Services\MissionEngine;
use App\Services\MissionFocusService;
use App\Services\StreakService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MissionFoundationFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_generation_covers_once_daily_weekly_and_event_window_scopes(): void
    {
        $user = User::factory()->create();

        MissionTemplate::query()->create([
            'key' => 'mission.once.profile',
            'title' => 'Completer son profil',
            'event_type' => 'profile.completed',
            'target_count' => 1,
            'scope' => MissionTemplate::SCOPE_ONCE,
            'start_at' => now()->subDay(),
            'end_at' => now()->addDays(30),
            'rewards' => ['xp' => 100, 'points' => 50],
            'is_active' => true,
        ]);

        MissionTemplate::query()->create([
            'key' => 'mission.daily.login',
            'title' => 'Connexion du jour',
            'event_type' => 'login.daily',
            'target_count' => 1,
            'scope' => MissionTemplate::SCOPE_DAILY,
            'difficulty' => 'simple',
            'rewards' => ['xp' => 20, 'points' => 10],
            'is_active' => true,
        ]);

        MissionTemplate::query()->create([
            'key' => 'mission.weekly.comments',
            'title' => 'Commentaires de la semaine',
            'event_type' => 'clip.comment',
            'target_count' => 3,
            'scope' => MissionTemplate::SCOPE_WEEKLY,
            'rewards' => ['xp' => 70, 'points' => 35],
            'is_active' => true,
        ]);

        MissionTemplate::query()->create([
            'key' => 'mission.event.clip-share',
            'title' => 'Partager pendant l event',
            'event_type' => 'clip.share',
            'target_count' => 1,
            'scope' => MissionTemplate::SCOPE_EVENT_WINDOW,
            'start_at' => now()->subHour(),
            'end_at' => now()->addHour(),
            'rewards' => ['xp' => 50, 'points' => 25],
            'is_active' => true,
        ]);

        $result = app(EnsureCurrentMissionInstancesAction::class)->execute($user);

        $this->assertSame(1, $result['once']);
        $this->assertSame(1, $result['daily']);
        $this->assertSame(1, $result['weekly']);
        $this->assertSame(1, $result['event_window']);
        $this->assertSame(4, MissionInstance::query()->count());
        $this->assertSame(4, UserMission::query()->where('user_id', $user->id)->count());
    }

    public function test_mission_completion_grants_standardized_xp_and_points_once(): void
    {
        $user = User::factory()->create();

        MissionTemplate::query()->create([
            'key' => 'mission.daily.clip-like',
            'title' => 'Liker un clip',
            'event_type' => 'clip.like',
            'target_count' => 1,
            'scope' => MissionTemplate::SCOPE_ONCE,
            'rewards' => ['xp' => 120, 'points' => 60],
            'is_active' => true,
        ]);

        app(EnsureCurrentMissionInstancesAction::class)->execute($user);

        app(MissionEngine::class)->recordEvent($user, 'clip.like', 1, [
            'event_key' => 'clip.like.test.1',
            'subject_type' => 'clip',
            'subject_id' => '1',
        ]);

        app(MissionEngine::class)->recordEvent($user, 'clip.like', 1, [
            'event_key' => 'clip.like.test.1',
            'subject_type' => 'clip',
            'subject_id' => '1',
        ]);

        $mission = UserMission::query()->where('user_id', $user->id)->firstOrFail();
        $user->refresh()->load('progress', 'rewardWallet');

        $this->assertNotNull($mission->completed_at);
        $this->assertNotNull($mission->rewarded_at);
        $this->assertSame(1, MissionCompletion::query()->where('user_id', $user->id)->count());
        $this->assertSame(1, MissionEventRecord::query()->where('user_id', $user->id)->where('event_type', 'clip.like')->count());
        $this->assertSame(120, (int) $user->progress?->total_xp);
        $this->assertSame(60, (int) $user->rewardWallet?->balance);
    }

    public function test_mission_progress_and_completion_create_live_notification_payloads(): void
    {
        $user = User::factory()->create();

        MissionTemplate::query()->create([
            'key' => 'mission.notify.clip-comment',
            'title' => 'Commenter deux clips',
            'event_type' => 'clip.comment',
            'target_count' => 2,
            'scope' => MissionTemplate::SCOPE_ONCE,
            'rewards' => ['xp' => 40, 'points' => 20],
            'is_active' => true,
        ]);

        app(EnsureCurrentMissionInstancesAction::class)->execute($user);

        app(MissionEngine::class)->recordEvent($user, 'clip.comment', 1, [
            'event_key' => 'clip.comment.notify.1',
            'subject_type' => 'clip-comment',
            'subject_id' => '1',
        ]);

        $progressNotification = Notification::query()
            ->where('user_id', $user->id)
            ->where('category', NotificationCategory::MISSION->value)
            ->latest('id')
            ->firstOrFail();

        $this->assertSame('Mission en progression', $progressNotification->title);
        $this->assertSame('progress', $progressNotification->data['toast_kind'] ?? null);
        $this->assertSame(1, $progressNotification->data['progress_count'] ?? null);
        $this->assertSame(2, $progressNotification->data['target_count'] ?? null);

        app(MissionEngine::class)->recordEvent($user, 'clip.comment', 1, [
            'event_key' => 'clip.comment.notify.2',
            'subject_type' => 'clip-comment',
            'subject_id' => '2',
        ]);

        $completionNotification = Notification::query()
            ->where('user_id', $user->id)
            ->where('category', NotificationCategory::MISSION->value)
            ->latest('id')
            ->firstOrFail();

        $this->assertSame('Mission terminee', $completionNotification->title);
        $this->assertSame('completed', $completionNotification->data['toast_kind'] ?? null);
        $this->assertSame(40, $completionNotification->data['rewards_xp'] ?? null);
        $this->assertSame(20, $completionNotification->data['rewards_points'] ?? null);
    }

    public function test_user_cannot_focus_more_than_three_missions(): void
    {
        $user = User::factory()->create();
        $service = app(MissionFocusService::class);

        $templates = collect(range(1, 4))->map(function (int $index): MissionTemplate {
            return MissionTemplate::query()->create([
                'key' => 'mission.focus.'.$index,
                'title' => 'Focus '.$index,
                'event_type' => 'clip.like',
                'target_count' => 1,
                'scope' => MissionTemplate::SCOPE_ONCE,
                'rewards' => ['xp' => 10, 'points' => 5],
                'is_active' => true,
            ]);
        });

        $service->add($user, $templates[0]);
        $service->add($user, $templates[1]);
        $service->add($user, $templates[2]);

        $this->expectException(\RuntimeException::class);
        $service->add($user, $templates[3]);
    }

    public function test_user_cannot_focus_template_without_current_user_mission(): void
    {
        $user = User::factory()->create();
        $template = MissionTemplate::query()->create([
            'key' => 'mission.focus.unavailable',
            'title' => 'Focus indisponible',
            'event_type' => 'clip.like',
            'target_count' => 1,
            'scope' => MissionTemplate::SCOPE_EVENT_WINDOW,
            'start_at' => now()->addDay(),
            'end_at' => now()->addDays(2),
            'rewards' => ['xp' => 10, 'points' => 5],
            'is_active' => true,
        ]);

        $this->expectException(\RuntimeException::class);

        app(MissionFocusService::class)->add($user, $template);
    }

    public function test_discovery_missions_are_exposed_first_in_catalog_payload(): void
    {
        $user = User::factory()->create();

        MissionTemplate::query()->create([
            'key' => 'mission.discovery',
            'title' => 'Mission decouverte',
            'event_type' => 'login.daily',
            'target_count' => 1,
            'scope' => MissionTemplate::SCOPE_DAILY,
            'is_discovery' => true,
            'sort_order' => 1,
            'rewards' => ['xp' => 30, 'points' => 15],
            'is_active' => true,
        ]);

        MissionTemplate::query()->create([
            'key' => 'mission.standard',
            'title' => 'Mission standard',
            'event_type' => 'clip.comment',
            'target_count' => 2,
            'scope' => MissionTemplate::SCOPE_DAILY,
            'sort_order' => 99,
            'rewards' => ['xp' => 40, 'points' => 20],
            'is_active' => true,
        ]);

        $payload = app(MissionCatalogService::class)->dashboardPayload($user);

        $this->assertSame('mission.discovery', $payload['discovery'][0]['key']);
    }

    public function test_focus_slots_are_freed_when_mission_is_no_longer_available(): void
    {
        $user = User::factory()->create();
        $service = app(MissionFocusService::class);

        $template = MissionTemplate::query()->create([
            'key' => 'mission.focus.expired',
            'title' => 'Mission expiree',
            'event_type' => 'clip.share',
            'target_count' => 1,
            'scope' => MissionTemplate::SCOPE_EVENT_WINDOW,
            'start_at' => now()->subHours(2),
            'end_at' => now()->addHour(),
            'rewards' => ['xp' => 15, 'points' => 10],
            'is_active' => true,
        ]);

        app(EnsureCurrentMissionInstancesAction::class)->execute($user);
        $service->add($user, $template);
        $this->assertCount(1, $service->forUser($user));

        $this->travel(2)->hours();

        $this->assertCount(0, $service->forUser($user));
    }

    public function test_focus_added_event_can_complete_launch_focus_mission(): void
    {
        $user = User::factory()->create();
        $template = MissionTemplate::query()->create([
            'key' => 'mission.launch.focus',
            'title' => 'Premier focus personnel',
            'event_type' => 'mission.focus.added',
            'target_count' => 1,
            'scope' => MissionTemplate::SCOPE_ONCE,
            'rewards' => ['xp' => 70, 'points' => 50],
            'is_active' => true,
        ]);

        app(EnsureCurrentMissionInstancesAction::class)->execute($user);
        app(MissionFocusService::class)->add($user, $template);

        $mission = UserMission::query()->where('user_id', $user->id)->whereHas('instance.template', fn ($query) => $query->where('key', 'mission.launch.focus'))->firstOrFail();

        $this->assertNotNull($mission->completed_at);
        $this->assertDatabaseHas('mission_event_records', [
            'user_id' => $user->id,
            'event_type' => 'mission.focus.added',
        ]);
    }

    public function test_activity_triple_day_signal_is_derived_from_three_real_action_types(): void
    {
        $user = User::factory()->create();

        MissionTemplate::query()->create([
            'key' => 'mission.activity.triple',
            'title' => 'Triple d actions',
            'event_type' => 'activity.triple.day',
            'target_count' => 1,
            'scope' => MissionTemplate::SCOPE_DAILY,
            'rewards' => ['xp' => 90, 'points' => 70],
            'is_active' => true,
        ]);

        app(EnsureCurrentMissionInstancesAction::class)->execute($user);

        app(MissionEngine::class)->recordEvent($user, 'clip.like', 1, ['event_key' => 'triple.like']);
        app(MissionEngine::class)->recordEvent($user, 'clip.comment', 1, ['event_key' => 'triple.comment']);
        app(MissionEngine::class)->recordEvent($user, 'bet.placed', 1, ['event_key' => 'triple.bet', 'stake_points' => 20]);

        $mission = UserMission::query()->where('user_id', $user->id)->firstOrFail();

        $this->assertNotNull($mission->completed_at);
        $this->assertDatabaseHas('mission_event_records', [
            'user_id' => $user->id,
            'event_type' => 'activity.triple.day',
        ]);
    }

    public function test_login_returned_event_can_complete_return_mission(): void
    {
        $user = User::factory()->create();

        MissionTemplate::query()->create([
            'key' => 'mission.returned',
            'title' => 'Revenir plus fort',
            'event_type' => 'login.returned',
            'target_count' => 1,
            'scope' => MissionTemplate::SCOPE_ONCE,
            'constraints' => ['min_absence_days' => 3],
            'rewards' => ['xp' => 100, 'points' => 80],
            'is_active' => true,
        ]);

        UserLoginStreak::query()->create([
            'user_id' => $user->id,
            'current_streak' => 4,
            'longest_streak' => 7,
            'last_login_on' => now()->subDays(5)->toDateString(),
            'current_multiplier' => 1.10,
            'last_reward_points' => 20,
            'streak_started_at' => now()->subDays(10),
        ]);

        app(EnsureCurrentMissionInstancesAction::class)->execute($user);
        app(StreakService::class)->handleLogin($user);

        $mission = UserMission::query()->where('user_id', $user->id)->firstOrFail();

        $this->assertNotNull($mission->completed_at);
        $this->assertDatabaseHas('mission_event_records', [
            'user_id' => $user->id,
            'event_type' => 'login.returned',
        ]);
    }

    public function test_level_and_rank_missions_progress_from_real_xp_awards(): void
    {
        $user = User::factory()->create();

        MissionTemplate::query()->create([
            'key' => 'mission.level.five',
            'title' => 'Cap niveau 5',
            'event_type' => 'progress.level.reached',
            'target_count' => 1,
            'scope' => MissionTemplate::SCOPE_ONCE,
            'constraints' => ['min_level' => 5],
            'rewards' => ['xp' => 220, 'points' => 160],
            'is_active' => true,
        ]);

        MissionTemplate::query()->create([
            'key' => 'mission.rank.argent',
            'title' => 'Cap Argent',
            'event_type' => 'progress.rank.reached',
            'target_count' => 1,
            'scope' => MissionTemplate::SCOPE_ONCE,
            'constraints' => ['required_rank' => 'argent'],
            'rewards' => ['xp' => 380, 'points' => 280],
            'is_active' => true,
        ]);

        app(EnsureCurrentMissionInstancesAction::class)->execute($user);
        app(AddPointsAction::class)->execute($user, PointsTransaction::KIND_XP, 1500, 'test.level', 'xp-1500');

        $completedKeys = UserMission::query()
            ->where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->with('instance.template')
            ->get()
            ->pluck('instance.template.key')
            ->all();

        $this->assertContains('mission.level.five', $completedKeys);
        $this->assertContains('mission.rank.argent', $completedKeys);
        $this->assertDatabaseHas('mission_event_records', [
            'user_id' => $user->id,
            'event_type' => 'progress.level.reached',
        ]);
        $this->assertDatabaseHas('mission_event_records', [
            'user_id' => $user->id,
            'event_type' => 'progress.rank.reached',
        ]);
    }

    public function test_catalog_can_filter_active_missions_by_status_and_type(): void
    {
        $user = User::factory()->create();

        MissionTemplate::query()->create([
            'key' => 'mission.filter.community',
            'title' => 'Commenter la communaute',
            'event_type' => 'clip.comment',
            'target_count' => 2,
            'scope' => MissionTemplate::SCOPE_DAILY,
            'type' => 'repeatable',
            'difficulty' => 'medium',
            'rewards' => ['xp' => 40, 'points' => 20],
            'is_active' => true,
        ]);

        MissionTemplate::query()->create([
            'key' => 'mission.filter.event',
            'title' => 'Mission event',
            'event_type' => 'clip.share',
            'target_count' => 1,
            'scope' => MissionTemplate::SCOPE_EVENT_WINDOW,
            'type' => 'event',
            'difficulty' => 'special',
            'start_at' => now()->subHour(),
            'end_at' => now()->addHour(),
            'rewards' => ['xp' => 90, 'points' => 45],
            'is_active' => true,
        ]);

        $payload = app(MissionCatalogService::class)->dashboardPayload($user, [
            'type' => 'event',
            'status' => 'in_progress',
        ]);

        $this->assertCount(1, $payload['active']);
        $this->assertSame('mission.filter.event', $payload['active'][0]['key']);
    }

    public function test_templates_outside_availability_window_are_not_generated(): void
    {
        $user = User::factory()->create();

        MissionTemplate::query()->create([
            'key' => 'mission.future.daily',
            'title' => 'Future daily',
            'event_type' => 'login.daily',
            'target_count' => 1,
            'scope' => MissionTemplate::SCOPE_DAILY,
            'start_at' => now()->addDay(),
            'end_at' => now()->addDays(5),
            'rewards' => ['xp' => 20, 'points' => 10],
            'is_active' => true,
        ]);

        $result = app(EnsureCurrentMissionInstancesAction::class)->execute($user);

        $this->assertSame(0, $result['daily']);
        $this->assertSame(0, UserMission::query()->where('user_id', $user->id)->count());
    }

    public function test_claim_required_mission_grants_rewards_only_when_claimed(): void
    {
        $user = User::factory()->create();

        MissionTemplate::query()->create([
            'key' => 'mission.claim.required',
            'title' => 'Mission claim',
            'event_type' => 'clip.comment',
            'target_count' => 1,
            'scope' => MissionTemplate::SCOPE_ONCE,
            'requires_claim' => true,
            'rewards' => ['xp' => 80, 'points' => 35],
            'is_active' => true,
        ]);

        app(EnsureCurrentMissionInstancesAction::class)->execute($user);

        app(MissionEngine::class)->recordEvent($user, 'clip.comment', 1, [
            'event_key' => 'clip.comment.claim.1',
            'subject_type' => 'clip-comment',
            'subject_id' => '1',
        ]);

        $mission = UserMission::query()->where('user_id', $user->id)->firstOrFail();
        $user->refresh()->load('progress', 'rewardWallet');

        $this->assertNotNull($mission->completed_at);
        $this->assertNull($mission->rewarded_at);
        $this->assertNull($mission->claimed_at);
        $this->assertSame(0, (int) ($user->progress?->total_xp ?? 0));
        $this->assertSame(0, (int) ($user->rewardWallet?->balance ?? 0));

        $this->actingAs($user)->post(route('missions.claim', $mission))->assertRedirect();

        $mission->refresh();
        $user->refresh()->load('progress', 'rewardWallet');

        $this->assertNotNull($mission->rewarded_at);
        $this->assertNotNull($mission->claimed_at);
        $this->assertSame(80, (int) $user->progress?->total_xp);
        $this->assertSame(35, (int) $user->rewardWallet?->balance);

        $this->actingAs($user)->post(route('missions.claim', $mission))->assertRedirect();

        $this->assertSame(1, MissionCompletion::query()->where('user_id', $user->id)->count());
        $this->assertSame(35, (int) $user->fresh()->rewardWallet?->balance);
    }

    public function test_admin_can_create_and_update_standardized_mission_templates(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $storeResponse = $this->actingAs($admin)->post(route('admin.missions.store'), [
            'key' => 'mission.admin.standardized',
            'title' => 'Mission admin',
            'short_description' => 'Version courte',
            'description' => 'Description complete',
            'long_description' => 'Description longue',
            'category' => 'community',
            'type' => 'repeatable',
            'event_type' => 'clip_like',
            'target_count' => 2,
            'scope' => MissionTemplate::SCOPE_DAILY,
            'difficulty' => 'medium',
            'estimated_minutes' => 12,
            'rewards_xp' => 80,
            'rewards_points' => 45,
            'is_discovery' => 1,
            'is_featured' => 1,
            'sort_order' => 5,
            'is_active' => 1,
        ]);

        $storeResponse->assertRedirect();

        $template = MissionTemplate::query()->where('key', 'mission.admin.standardized')->firstOrFail();

        $this->assertSame(['xp' => 80, 'points' => 45], $template->rewards);
        $this->assertSame('clip.like', $template->event_type);
        $this->assertTrue($template->is_discovery);
        $this->assertTrue($template->is_featured);

        $updateResponse = $this->actingAs($admin)->put(route('admin.missions.update', $template->id), [
            'key' => 'mission.admin.standardized',
            'title' => 'Mission admin v2',
            'short_description' => 'Version courte v2',
            'description' => 'Description v2',
            'long_description' => 'Description longue v2',
            'category' => 'bets',
            'type' => 'event',
            'event_type' => 'bet_won',
            'target_count' => 4,
            'scope' => MissionTemplate::SCOPE_WEEKLY,
            'difficulty' => 'hard',
            'estimated_minutes' => 25,
            'rewards_xp' => 150,
            'rewards_points' => 90,
            'is_discovery' => 0,
            'is_featured' => 0,
            'sort_order' => 12,
            'is_active' => 1,
        ]);

        $updateResponse->assertRedirect();

        $template->refresh();

        $this->assertSame('Mission admin v2', $template->title);
        $this->assertSame('bet.won', $template->event_type);
        $this->assertSame(['xp' => 150, 'points' => 90], $template->rewards);
        $this->assertSame('bets', $template->category);
        $this->assertSame('hard', $template->difficulty);
    }

    public function test_admin_missions_screen_renders_new_foundation_controls(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.missions.index'))
            ->assertOk()
            ->assertSee('Pilotage missions')
            ->assertSee('Mission associee')
            ->assertSee('Regenerer la fenetre evenement')
            ->assertSee('Reparer et resynchroniser');
    }
}
