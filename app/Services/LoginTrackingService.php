<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\UserStreak;
use Illuminate\Support\Facades\DB;

class LoginTrackingService
{
    public function __construct(
        private readonly EventTrackingService $eventTrackingService
    ) {
    }

    public function onSuccessfulLogin(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $today = now()->toDateString();

            /** @var UserStreak|null $streak */
            $streak = UserStreak::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if ($streak === null) {
                $streak = UserStreak::query()->create([
                    'user_id' => $user->id,
                    'current_streak' => 1,
                    'longest_streak' => 1,
                    'last_login_date' => $today,
                ]);

                $this->eventTrackingService->trackAction($user, 'user_logged_in', ['date' => $today]);
                $this->eventTrackingService->trackAction($user, 'streak_updated', [
                    'current_streak' => 1,
                    'longest_streak' => 1,
                    'date' => $today,
                ]);

                return;
            }

            $lastLogin = $streak->last_login_date?->toDateString();

            if ($lastLogin === $today) {
                return;
            }

            $yesterday = now()->subDay()->toDateString();

            $nextCurrent = $lastLogin === $yesterday
                ? $streak->current_streak + 1
                : 1;

            $streak->current_streak = $nextCurrent;
            $streak->longest_streak = max($streak->longest_streak, $nextCurrent);
            $streak->last_login_date = $today;
            $streak->save();

            $this->eventTrackingService->trackAction($user, 'user_logged_in', ['date' => $today]);
            $this->eventTrackingService->trackAction($user, 'streak_updated', [
                'current_streak' => $streak->current_streak,
                'longest_streak' => $streak->longest_streak,
                'date' => $today,
            ]);
        });
    }
}
