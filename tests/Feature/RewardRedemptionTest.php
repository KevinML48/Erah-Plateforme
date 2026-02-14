<?php
declare(strict_types=1);

use App\Enums\PointTransactionType;
use App\Enums\RewardRedemptionStatus;
use App\Exceptions\OutOfStockException;
use App\Exceptions\RedemptionNotAllowedException;
use App\Models\PointLog;
use App\Models\Reward;
use App\Models\User;
use App\Services\RedemptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('user can redeem if enough points and stock', function (): void {
    $user = User::factory()->create(['points_balance' => 1000]);
    $reward = Reward::query()->create([
        'name' => 'Maillot ERAH',
        'slug' => 'maillot-erah',
        'points_cost' => 500,
        'stock' => 5,
        'is_active' => true,
    ]);

    $redemption = app(RedemptionService::class)->createRedemption($user, $reward, []);

    $user->refresh();
    $reward->refresh();

    expect($redemption->status)->toBe(RewardRedemptionStatus::Pending);
    expect($user->points_balance)->toBe(500);
    expect($reward->stock)->toBe(4);
});

it('redeem decrements stock and debits points exactly once', function (): void {
    $user = User::factory()->create(['points_balance' => 1200]);
    $reward = Reward::query()->create([
        'name' => 'Mousepad ERAH',
        'slug' => 'mousepad-erah',
        'points_cost' => 400,
        'stock' => 2,
        'is_active' => true,
    ]);

    app(RedemptionService::class)->createRedemption($user, $reward, []);

    $user->refresh();
    $reward->refresh();

    expect($user->points_balance)->toBe(800);
    expect($reward->stock)->toBe(1);
    expect(PointLog::query()->where('type', PointTransactionType::RewardRedeem->value)->count())->toBe(1);
});

it('reject refunds points and restores stock idempotently', function (): void {
    $user = User::factory()->create(['points_balance' => 700]);
    $admin = User::factory()->create(['is_admin' => true]);
    $reward = Reward::query()->create([
        'name' => 'Hoodie ERAH',
        'slug' => 'hoodie-erah',
        'points_cost' => 300,
        'stock' => 1,
        'is_active' => true,
    ]);

    $service = app(RedemptionService::class);
    $redemption = $service->createRedemption($user, $reward, []);

    $service->rejectRedemption($admin, $redemption, 'rupture');
    $service->rejectRedemption($admin, $redemption, 'rupture');

    $user->refresh();
    $reward->refresh();
    $redemption->refresh();

    expect($redemption->status)->toBe(RewardRedemptionStatus::Rejected);
    expect($user->points_balance)->toBe(700);
    expect($reward->stock)->toBe(1);
    expect(PointLog::query()->where('type', PointTransactionType::RewardRefund->value)->count())->toBe(1);
});

it('cancel pending refunds points and restores stock', function (): void {
    $user = User::factory()->create(['points_balance' => 500]);
    $reward = Reward::query()->create([
        'name' => 'Casquette ERAH',
        'slug' => 'casquette-erah',
        'points_cost' => 200,
        'stock' => 1,
        'is_active' => true,
    ]);

    $service = app(RedemptionService::class);
    $redemption = $service->createRedemption($user, $reward, []);
    $service->cancelRedemption($user, $redemption);

    $user->refresh();
    $reward->refresh();
    $redemption->refresh();

    expect($redemption->status)->toBe(RewardRedemptionStatus::Cancelled);
    expect($user->points_balance)->toBe(500);
    expect($reward->stock)->toBe(1);
});

it('cannot redeem if out of stock', function (): void {
    $user = User::factory()->create(['points_balance' => 2000]);
    $reward = Reward::query()->create([
        'name' => 'Sticker',
        'slug' => 'sticker',
        'points_cost' => 100,
        'stock' => 0,
        'is_active' => true,
    ]);

    expect(fn () => app(RedemptionService::class)->createRedemption($user, $reward, []))
        ->toThrow(OutOfStockException::class);
});

it('cannot ship if not approved', function (): void {
    $user = User::factory()->create(['points_balance' => 1000]);
    $admin = User::factory()->create(['is_admin' => true]);
    $reward = Reward::query()->create([
        'name' => 'Keycap',
        'slug' => 'keycap',
        'points_cost' => 300,
        'stock' => 10,
        'is_active' => true,
    ]);

    $redemption = app(RedemptionService::class)->createRedemption($user, $reward, []);

    expect(fn () => app(RedemptionService::class)->markShipped($admin, $redemption, 'TRACK-1'))
        ->toThrow(RedemptionNotAllowedException::class);
});

