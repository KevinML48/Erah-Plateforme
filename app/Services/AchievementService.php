<?php

namespace App\Services;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Notifications\NotifyAction;
use App\Domain\Notifications\Enums\NotificationCategory;
use App\Models\Achievement;
use App\Models\Bet;
use App\Models\ClipComment;
use App\Models\ClipView;
use App\Models\DuelResult;
use App\Models\User;
use App\Models\UserAchievement;
use Illuminate\Support\Collection;

class AchievementService
{
    public function __construct(
        private readonly RewardGrantService $rewardGrantService,
        private readonly NotifyAction $notifyAction,
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    /**
     * @return Collection<int, UserAchievement>
     */
    public function sync(User $user): Collection
    {
        $unlocked = collect();
        $definitions = Achievement::query()->active()->orderBy('sort_order')->get();

        foreach ($definitions as $achievement) {
            $progressValue = $this->metricValue($user, (string) $achievement->metric);

            $userAchievement = UserAchievement::query()->firstOrNew([
                'achievement_id' => $achievement->id,
                'user_id' => $user->id,
            ]);

            $userAchievement->progress_value = $progressValue;
            $userAchievement->meta = [
                'metric' => $achievement->metric,
                'threshold' => $achievement->threshold,
            ];

            if ($progressValue >= (int) $achievement->threshold && $userAchievement->unlocked_at === null) {
                $userAchievement->unlocked_at = now();
                $userAchievement->save();

                $rewards = is_array($achievement->rewards) ? $achievement->rewards : [];
                if ($rewards !== []) {
                    $this->rewardGrantService->grant(
                        user: $user,
                        domain: 'achievements',
                        action: 'unlock',
                        dedupeKey: 'achievement.unlock.'.$user->id.'.'.$achievement->key,
                        rewards: [
                            'xp' => (int) ($rewards['xp'] ?? 0),
                            'points' => (int) ($rewards['points'] ?? $rewards['reward_points'] ?? 0),
                            'rank_points' => (int) ($rewards['rank_points'] ?? 0),
                        ],
                        subjectType: Achievement::class,
                        subjectId: (string) $achievement->id,
                    );
                }

                $this->notifyAction->execute(
                    user: $user,
                    category: NotificationCategory::ACHIEVEMENT->value,
                    title: 'Succes debloque',
                    message: 'Vous debloquez "'.$achievement->name.'".',
                    data: [
                        'achievement_key' => $achievement->key,
                        'achievement_name' => $achievement->name,
                    ],
                );

                $this->storeAuditLogAction->execute(
                    action: 'achievements.unlocked',
                    actor: $user,
                    target: $userAchievement,
                    context: [
                        'achievement_key' => $achievement->key,
                        'progress_value' => $progressValue,
                    ],
                );

                $unlocked->push($userAchievement);

                continue;
            }

            $userAchievement->save();
        }

        return $unlocked;
    }

    public function seedDefaults(): void
    {
        foreach ((array) config('community.achievements', []) as $definition) {
            Achievement::query()->updateOrCreate(
                ['key' => $definition['key']],
                [
                    'name' => $definition['name'],
                    'description' => $definition['description'] ?? null,
                    'type' => $definition['type'] ?? 'communaute',
                    'metric' => $definition['metric'] ?? $definition['key'],
                    'threshold' => (int) ($definition['threshold'] ?? 1),
                    'badge_label' => $definition['badge_label'] ?? null,
                    'rewards' => $definition['rewards'] ?? null,
                    'meta' => null,
                    'is_active' => true,
                    'sort_order' => (int) ($definition['sort_order'] ?? 0),
                ],
            );
        }
    }

    private function metricValue(User $user, string $metric): int
    {
        return match ($metric) {
            'clip_views' => ClipView::query()->where('user_id', $user->id)->count(),
            'clip_comments' => ClipComment::query()->where('user_id', $user->id)->count(),
            'bets_won' => Bet::query()->where('user_id', $user->id)->where('status', Bet::STATUS_WON)->count(),
            'duels_won' => DuelResult::query()->where('winner_user_id', $user->id)->count(),
            'total_xp' => (int) ($user->progress?->total_xp ?? $user->progress()->value('total_xp') ?? 0),
            default => 0,
        };
    }
}
