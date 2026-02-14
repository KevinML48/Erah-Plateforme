<?php
declare(strict_types=1);

use App\Enums\PointTransactionType;
use App\Exceptions\InsufficientPointsException;
use App\Exceptions\PointTransactionAlreadyProcessedException;
use App\Jobs\RefreshLeaderboardStatsForUserJob;
use App\Models\Rank;
use App\Models\User;
use App\Services\PointService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Rank::query()->create(['name' => 'Bronze', 'slug' => 'bronze', 'min_points' => 0]);
    Rank::query()->create(['name' => 'Silver', 'slug' => 'silver', 'min_points' => 1000]);
});

it('adds points, creates log, updates rank and dispatches stat refresh job', function (): void {
    Queue::fake();

    $user = User::factory()->create([
        'points_balance' => 950,
    ]);

    $service = app(PointService::class);
    $service->addPoints(
        user: $user,
        amount: 100,
        type: PointTransactionType::MissionComplete->value,
        description: 'Mission reward',
        referenceId: 10,
        referenceType: 'mission',
        idempotencyKey: 'mission-10-user-'.$user->id
    );

    $user->refresh();

    expect($user->points_balance)->toBe(1050);
    expect($user->rank?->slug)->toBe('silver');
    expect($user->pointLogs()->count())->toBe(1);

    Queue::assertPushed(RefreshLeaderboardStatsForUserJob::class);
});

it('prevents duplicate idempotent transactions', function (): void {
    $user = User::factory()->create([
        'points_balance' => 500,
    ]);

    $service = app(PointService::class);
    $idempotencyKey = 'dup-key-'.$user->id;

    $service->addPoints(
        user: $user,
        amount: 50,
        type: PointTransactionType::MissionComplete->value,
        idempotencyKey: $idempotencyKey
    );

    expect(fn () => $service->addPoints(
        user: $user,
        amount: 50,
        type: PointTransactionType::MissionComplete->value,
        idempotencyKey: $idempotencyKey
    ))->toThrow(PointTransactionAlreadyProcessedException::class);
});

it('prevents negative balance on debit', function (): void {
    $user = User::factory()->create([
        'points_balance' => 200,
    ]);

    $service = app(PointService::class);

    expect(fn () => $service->removePoints(
        user: $user,
        amount: 500,
        type: PointTransactionType::RewardPurchase->value
    ))->toThrow(InsufficientPointsException::class);
});

