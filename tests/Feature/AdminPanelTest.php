<?php
declare(strict_types=1);

use App\Enums\PointTransactionType;
use App\Enums\RewardRedemptionStatus;
use App\Models\AdminAuditLog;
use App\Models\PointLog;
use App\Models\Reward;
use App\Models\User;
use App\Services\RedemptionService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(RolesAndPermissionsSeeder::class);
});

it('blocks admin panel access for user without admin access permission', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/admin');

    $response->assertForbidden();
});

it('allows admin panel access for user with admin access permission', function (): void {
    $admin = User::factory()->create();
    $admin->givePermissionTo('admin.access');

    $response = $this->actingAs($admin)->get('/admin');

    $response->assertOk();
});

it('adjust points creates point log and admin audit log', function (): void {
    $admin = User::factory()->create();
    $admin->givePermissionTo(['admin.access', 'points.adjust']);

    $target = User::factory()->create([
        'email' => 'target@example.com',
        'points_balance' => 100,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.points.store'), [
            'email' => $target->email,
            'amount' => 250,
            'reason' => 'Manual balance correction',
        ])
        ->assertRedirect(route('admin.points.index'));

    $target->refresh();

    expect($target->points_balance)->toBe(350);

    expect(PointLog::query()
        ->where('user_id', $target->id)
        ->where('type', PointTransactionType::AdminAdjustment->value)
        ->where('amount', 250)
        ->count())->toBe(1);

    expect(AdminAuditLog::query()
        ->where('actor_user_id', $admin->id)
        ->where('action', 'points.adjust')
        ->where('entity_type', 'user')
        ->where('entity_id', $target->id)
        ->count())->toBe(1);
});

it('reject redemption is idempotent and logs admin audit once', function (): void {
    $admin = User::factory()->create();
    $admin->givePermissionTo(['admin.access', 'redemptions.manage']);

    $user = User::factory()->create(['points_balance' => 1200]);

    $reward = Reward::query()->create([
        'name' => 'Test Reward',
        'slug' => 'test-reward',
        'points_cost' => 300,
        'stock' => 1,
        'is_active' => true,
    ]);

    $redemption = app(RedemptionService::class)->createRedemption($user, $reward);

    $this->actingAs($admin)
        ->post(route('admin.redemptions.reject', $redemption), ['note' => 'Out of stock'])
        ->assertRedirect(route('admin.redemptions.index'));

    $this->actingAs($admin)
        ->post(route('admin.redemptions.reject', $redemption), ['note' => 'Out of stock'])
        ->assertRedirect(route('admin.redemptions.index'));

    $user->refresh();
    $reward->refresh();
    $redemption->refresh();

    expect($redemption->status)->toBe(RewardRedemptionStatus::Rejected);
    expect($user->points_balance)->toBe(1200);
    expect($reward->stock)->toBe(1);

    expect(PointLog::query()->where('type', PointTransactionType::RewardRefund->value)->count())->toBe(1);
    expect(AdminAuditLog::query()->where('action', 'redemption.reject')->where('entity_id', $redemption->id)->count())->toBe(1);
});
