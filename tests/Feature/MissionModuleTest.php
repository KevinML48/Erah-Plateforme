<?php
declare(strict_types=1);

use App\Enums\MissionClaimType;
use App\Enums\MissionCompletionRule;
use App\Enums\MissionRecurrence;
use App\Models\Mission;
use App\Models\MissionProgress;
use App\Models\PointLog;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserStreak;
use App\Services\EventTrackingService;
use App\Services\LoginTrackingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

uses(RefreshDatabase::class);

afterEach(function (): void {
    Mockery::close();
});

function createMissionWithSteps(array $missionData, array $steps): Mission {
    $mission = Mission::query()->create(array_merge([
        'title' => 'Mission '.Str::random(6),
        'slug' => Str::upper(Str::random(12)),
        'description' => 'Test mission',
        'points_reward' => 100,
        'recurrence' => MissionRecurrence::OneTime,
        'completion_rule' => MissionCompletionRule::All,
        'claim_type' => MissionClaimType::Auto,
        'is_active' => true,
    ], $missionData));

    $mission->steps()->createMany($steps);

    return $mission;
}

it('1) page views trigger progression', function (): void {
    $user = User::factory()->create();

    $mission = createMissionWithSteps([], [
        ['step_key' => 'page_viewed', 'step_value' => 'dashboard', 'label' => 'Dashboard', 'order' => 1],
    ]);

    $this->actingAs($user)->get(route('dashboard'))->assertOk();

    $progress = MissionProgress::query()->where('mission_id', $mission->id)->where('user_id', $user->id)->first();

    expect($progress)->not->toBeNull();
    expect((int) ($progress->progress_json['completed_steps'] ?? 0))->toBeGreaterThanOrEqual(1);
});

it('2) onboarding mission completes after 4 pages', function (): void {
    $user = User::factory()->create(['points_balance' => 0]);

    $mission = createMissionWithSteps([
        'slug' => 'ONBOARDING_APP_DISCOVERY_TEST',
        'points_reward' => 250,
    ], [
        ['step_key' => 'page_viewed', 'step_value' => 'dashboard', 'label' => 'Dashboard', 'order' => 1],
        ['step_key' => 'page_viewed', 'step_value' => 'matches', 'label' => 'Matches', 'order' => 2],
        ['step_key' => 'page_viewed', 'step_value' => 'rewards', 'label' => 'Rewards', 'order' => 3],
        ['step_key' => 'page_viewed', 'step_value' => 'leaderboard', 'label' => 'Leaderboard', 'order' => 4],
    ]);

    $this->actingAs($user)->get(route('dashboard'))->assertOk();
    $this->actingAs($user)->get(route('matches.index'))->assertOk();
    $this->actingAs($user)->get(route('rewards.index'))->assertOk();
    $this->actingAs($user)->get(route('leaderboard.all-time'))->assertOk();

    $progress = MissionProgress::query()->where('mission_id', $mission->id)->where('user_id', $user->id)->firstOrFail();

    expect($progress->completed_at)->not->toBeNull();
    expect($progress->awarded_points)->toBeTrue();

    $user->refresh();
    expect($user->points_balance)->toBe(250);
});

it('3) points are awarded only once', function (): void {
    $user = User::factory()->create(['points_balance' => 0]);

    createMissionWithSteps([
        'slug' => 'FIRST_PREDICTION_ONCE',
        'points_reward' => 120,
    ], [
        ['step_key' => 'prediction_created', 'step_value' => null, 'label' => 'Prediction', 'order' => 1],
    ]);

    Carbon::setTestNow('2026-02-15 10:00:00');
    app(EventTrackingService::class)->trackAction($user, 'prediction_created', ['id' => 1]);
    Carbon::setTestNow('2026-02-15 10:00:10');
    app(EventTrackingService::class)->trackAction($user, 'prediction_created', ['id' => 2]);
    Carbon::setTestNow();

    $user->refresh();

    expect($user->points_balance)->toBe(120);
    expect(PointLog::query()->where('user_id', $user->id)->where('type', 'mission_complete')->count())->toBe(1);
});

it('4) DAILY mission resets next day', function (): void {
    $user = User::factory()->create(['points_balance' => 0]);

    $mission = createMissionWithSteps([
        'slug' => 'DAILY_LOGIN_RESET',
        'recurrence' => MissionRecurrence::Daily,
        'points_reward' => 60,
    ], [
        ['step_key' => 'user_logged_in', 'step_value' => null, 'label' => 'Login', 'order' => 1],
    ]);

    Carbon::setTestNow('2026-02-15 10:00:00');
    app(LoginTrackingService::class)->onSuccessfulLogin($user);

    Carbon::setTestNow('2026-02-16 10:00:00');
    app(LoginTrackingService::class)->onSuccessfulLogin($user);

    Carbon::setTestNow();

    $user->refresh();
    expect($user->points_balance)->toBe(120);
    expect(MissionProgress::query()->where('mission_id', $mission->id)->where('user_id', $user->id)->count())->toBe(2);
});

it('5) WEEKLY mission resets next week', function (): void {
    $user = User::factory()->create(['points_balance' => 0]);

    $mission = createMissionWithSteps([
        'slug' => 'WEEKLY_RESET',
        'recurrence' => MissionRecurrence::Weekly,
        'points_reward' => 80,
    ], [
        ['step_key' => 'leaderboard_viewed', 'step_value' => null, 'label' => 'Leaderboard', 'order' => 1],
    ]);

    Carbon::setTestNow('2026-02-16 10:00:00'); // week 8
    app(EventTrackingService::class)->trackAction($user, 'leaderboard_viewed', ['type' => 'all_time']);

    Carbon::setTestNow('2026-02-23 10:00:00'); // week 9
    app(EventTrackingService::class)->trackAction($user, 'leaderboard_viewed', ['type' => 'all_time']);

    Carbon::setTestNow();

    expect(MissionProgress::query()->where('mission_id', $mission->id)->where('user_id', $user->id)->count())->toBe(2);
});

it('6) login streak works', function (): void {
    $user = User::factory()->create();

    Carbon::setTestNow('2026-02-15 10:00:00');
    app(LoginTrackingService::class)->onSuccessfulLogin($user);

    Carbon::setTestNow('2026-02-16 10:00:00');
    app(LoginTrackingService::class)->onSuccessfulLogin($user);

    Carbon::setTestNow('2026-02-18 10:00:00'); // break streak
    app(LoginTrackingService::class)->onSuccessfulLogin($user);

    Carbon::setTestNow();

    $streak = UserStreak::query()->where('user_id', $user->id)->firstOrFail();

    expect($streak->current_streak)->toBe(1);
    expect($streak->longest_streak)->toBe(2);
});

it('7) discord_link mission completes after OAuth callback', function (): void {
    createMissionWithSteps([
        'slug' => 'DISCORD_LINK_TEST',
        'points_reward' => 140,
    ], [
        ['step_key' => 'discord_linked', 'step_value' => null, 'label' => 'Discord linked', 'order' => 1],
    ]);

    $oauthUser = Mockery::mock(SocialiteUser::class);
    $oauthUser->shouldReceive('getEmail')->andReturn('discord.test@example.com');
    $oauthUser->shouldReceive('getId')->andReturn('discord_123');
    $oauthUser->shouldReceive('getName')->andReturn('Discord User');
    $oauthUser->shouldReceive('getNickname')->andReturn('DiscordUser');
    $oauthUser->shouldReceive('getAvatar')->andReturn('https://cdn.example.com/avatar.png');

    $driver = Mockery::mock();
    $driver->shouldReceive('user')->andReturn($oauthUser);

    Socialite::shouldReceive('driver')->with('discord')->andReturn($driver);

    $this->get(route('auth.callback', ['provider' => 'discord']))->assertRedirect(route('dashboard'));

    $user = User::query()->where('email', 'discord.test@example.com')->firstOrFail();
    $progress = MissionProgress::query()->where('user_id', $user->id)->firstOrFail();

    expect($progress->completed_at)->not->toBeNull();
    expect($progress->awarded_points)->toBeTrue();
});

it('8) daily cap blocks at threshold', function (): void {
    $user = User::factory()->create(['points_balance' => 0]);

    Setting::query()->updateOrCreate(
        ['key' => 'missions.daily_points_cap'],
        ['value' => ['cap' => 100], 'type' => 'json']
    );

    $missionA = createMissionWithSteps([
        'slug' => 'CAP_A',
        'recurrence' => MissionRecurrence::Daily,
        'points_reward' => 70,
    ], [
        ['step_key' => 'page_viewed', 'step_value' => 'dashboard', 'label' => 'Dashboard A', 'order' => 1],
    ]);

    $missionB = createMissionWithSteps([
        'slug' => 'CAP_B',
        'recurrence' => MissionRecurrence::Daily,
        'points_reward' => 40,
    ], [
        ['step_key' => 'page_viewed', 'step_value' => 'dashboard', 'label' => 'Dashboard B', 'order' => 1],
    ]);

    $this->actingAs($user)->get(route('dashboard'))->assertOk();

    $user->refresh();
    $awardedCount = MissionProgress::query()
        ->where('user_id', $user->id)
        ->whereIn('mission_id', [$missionA->id, $missionB->id])
        ->where('awarded_points', true)
        ->count();

    expect($user->points_balance)->toBeLessThanOrEqual(100);
    expect($awardedCount)->toBe(1);
});

it('9) ANY_N works correctly', function (): void {
    $user = User::factory()->create(['points_balance' => 0]);

    $mission = createMissionWithSteps([
        'slug' => 'ANY_N_TEST',
        'recurrence' => MissionRecurrence::Weekly,
        'completion_rule' => MissionCompletionRule::AnyN,
        'any_n' => 3,
        'points_reward' => 110,
    ], [
        ['step_key' => 'leaderboard_viewed', 'step_value' => null, 'label' => 'Leaderboard view', 'order' => 1],
    ]);

    Carbon::setTestNow('2026-02-15 10:00:00');
    app(EventTrackingService::class)->trackAction($user, 'leaderboard_viewed', ['hit' => 1]);
    Carbon::setTestNow('2026-02-15 10:00:10');
    app(EventTrackingService::class)->trackAction($user, 'leaderboard_viewed', ['hit' => 2]);

    $progress = MissionProgress::query()->where('mission_id', $mission->id)->where('user_id', $user->id)->firstOrFail();
    expect($progress->completed_at)->toBeNull();

    Carbon::setTestNow('2026-02-15 10:00:20');
    app(EventTrackingService::class)->trackAction($user, 'leaderboard_viewed', ['hit' => 3]);
    Carbon::setTestNow();

    $progress->refresh();
    expect($progress->completed_at)->not->toBeNull();
    expect($progress->awarded_points)->toBeTrue();
});

it('10) mission_completed event triggers COMPLETE_3_MISSIONS', function (): void {
    $user = User::factory()->create(['points_balance' => 0]);

    $target = createMissionWithSteps([
        'slug' => 'COMPLETE_3_MISSIONS_TARGET',
        'recurrence' => MissionRecurrence::Weekly,
        'completion_rule' => MissionCompletionRule::AnyN,
        'any_n' => 3,
        'points_reward' => 220,
    ], [
        ['step_key' => 'mission_completed', 'step_value' => null, 'label' => 'Complete missions', 'order' => 1],
    ]);

    createMissionWithSteps(['slug' => 'SOURCE_A', 'points_reward' => 10], [['step_key' => 'custom_a', 'step_value' => null, 'label' => 'A', 'order' => 1]]);
    createMissionWithSteps(['slug' => 'SOURCE_B', 'points_reward' => 10], [['step_key' => 'custom_b', 'step_value' => null, 'label' => 'B', 'order' => 1]]);
    createMissionWithSteps(['slug' => 'SOURCE_C', 'points_reward' => 10], [['step_key' => 'custom_c', 'step_value' => null, 'label' => 'C', 'order' => 1]]);

    Carbon::setTestNow('2026-02-15 10:00:00');
    app(EventTrackingService::class)->trackAction($user, 'custom_a');
    Carbon::setTestNow('2026-02-15 10:00:10');
    app(EventTrackingService::class)->trackAction($user, 'custom_b');
    Carbon::setTestNow('2026-02-15 10:00:20');
    app(EventTrackingService::class)->trackAction($user, 'custom_c');
    Carbon::setTestNow();

    $progress = MissionProgress::query()->where('mission_id', $target->id)->where('user_id', $user->id)->firstOrFail();
    expect($progress->completed_at)->not->toBeNull();
    expect($progress->awarded_points)->toBeTrue();
});
