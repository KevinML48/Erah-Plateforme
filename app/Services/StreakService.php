<?php

namespace App\Services;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Notifications\NotifyAction;
use App\Domain\Notifications\Enums\NotificationCategory;
use App\Models\User;
use App\Models\UserLoginStreak;
use Illuminate\Support\Facades\Schema;

class StreakService
{
    public function __construct(
        private readonly WalletService $walletService,
        private readonly NotifyAction $notifyAction,
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    public function handleLogin(User $user): UserLoginStreak
    {
        if (! $this->isAvailable()) {
            return new UserLoginStreak([
                'user_id' => $user->id,
                'current_streak' => 0,
                'longest_streak' => 0,
                'last_login_on' => null,
                'current_multiplier' => 1.00,
                'last_reward_points' => 0,
                'streak_started_at' => now(),
            ]);
        }

        $today = now()->toDateString();

        $streak = UserLoginStreak::query()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'current_streak' => 0,
                'longest_streak' => 0,
                'last_login_on' => null,
                'current_multiplier' => 1.00,
                'last_reward_points' => 0,
                'streak_started_at' => now(),
            ],
        );

        if ($streak->last_login_on?->toDateString() === $today) {
            return $streak;
        }

        $yesterday = now()->subDay()->toDateString();
        $streak->current_streak = $streak->last_login_on?->toDateString() === $yesterday
            ? (int) $streak->current_streak + 1
            : 1;

        if ($streak->current_streak === 1) {
            $streak->streak_started_at = now();
        }

        $streak->longest_streak = max((int) $streak->longest_streak, (int) $streak->current_streak);
        $streak->last_login_on = now()->toDateString();
        $streak->current_multiplier = $this->resolveMultiplier((int) $streak->current_streak);

        $rewardPoints = $this->resolveRewardPoints((int) $streak->current_streak);
        $streak->last_reward_points = $rewardPoints;
        $streak->save();

        if ($rewardPoints > 0) {
            $this->walletService->adjustRewardPoints(
                user: $user,
                amount: $rewardPoints,
                uniqueKey: 'streak.login.'.$user->id.'.'.$today,
                meta: [
                    'current_streak' => $streak->current_streak,
                    'date' => $today,
                ],
            );
        }

        $this->notifyAction->execute(
            user: $user,
            category: NotificationCategory::SYSTEM->value,
            title: 'Connexion du jour validee',
            message: 'Serie active: '.$streak->current_streak.' jour(s).',
            data: [
                'current_streak' => $streak->current_streak,
                'reward_points' => $rewardPoints,
                'xp_multiplier' => (float) $streak->current_multiplier,
            ],
        );

        $this->storeAuditLogAction->execute(
            action: 'streak.login.recorded',
            actor: $user,
            target: $streak,
            context: [
                'current_streak' => $streak->current_streak,
                'reward_points' => $rewardPoints,
                'multiplier' => $streak->current_multiplier,
            ],
        );

        return $streak;
    }

    public function xpMultiplierFor(User $user): float
    {
        if (! $this->isAvailable()) {
            return 1.0;
        }

        $streak = $user->relationLoaded('loginStreak')
            ? $user->loginStreak
            : $user->loginStreak()->first();

        return (float) ($streak?->current_multiplier ?? 1.0);
    }

    private function resolveRewardPoints(int $currentStreak): int
    {
        $rewards = collect((array) config('community.streak.rewards', []))
            ->sortKeys();

        return (int) ($rewards->get($currentStreak) ?? 0);
    }

    private function resolveMultiplier(int $currentStreak): float
    {
        return (float) collect((array) config('community.streak.xp_multiplier', []))
            ->sortKeys()
            ->filter(fn (float $multiplier, int $day): bool => $currentStreak >= $day)
            ->last();
    }

    private function isAvailable(): bool
    {
        return Schema::hasTable('user_login_streaks');
    }
}
