<?php

namespace App\Services;

use App\Models\Clip;
use App\Models\ClipComment;
use App\Models\ClipView;
use App\Models\User;

class ClipRewardService
{
    public function __construct(
        private readonly RewardGrantService $rewardGrantService,
        private readonly EventService $eventService,
        private readonly MissionEngine $missionEngine,
        private readonly AchievementService $achievementService
    ) {
    }

    public function recordView(Clip $clip, ?User $user = null, ?string $sessionId = null, ?string $ipHash = null): ClipView
    {
        $view = ClipView::query()->create([
            'clip_id' => $clip->id,
            'user_id' => $user?->id,
            'session_id' => $sessionId,
            'ip_hash' => $ipHash,
            'meta' => null,
            'viewed_at' => now(),
        ]);

        if (! $user) {
            return $view;
        }

        $dedupeKey = 'clips.view.'.$user->id.'.'.$clip->id;
        $dailyLimit = (int) config('community.clips.daily_limits.view', 10);

        if (! $this->rewardGrantService->wasGranted($dedupeKey)
            && $this->rewardGrantService->countForDay($user, 'clips', 'view') < $dailyLimit) {
            $rewards = $this->eventService->applyModifiers(
                (array) config('community.clips.rewards.view', []),
                'bonus_clips',
            );

            $this->rewardGrantService->grant(
                user: $user,
                domain: 'clips',
                action: 'view',
                dedupeKey: $dedupeKey,
                rewards: $rewards,
                subjectType: Clip::class,
                subjectId: (string) $clip->id,
            );
        }

        $this->missionEngine->recordEvent($user, 'clip.view');
        $this->achievementService->sync($user);

        return $view;
    }

    public function rewardLike(User $user, Clip $clip): void
    {
        $this->rewardOnce($user, $clip, 'like');
        $this->missionEngine->recordEvent($user, 'clip.like');
        $this->achievementService->sync($user);
    }

    public function rewardComment(User $user, Clip $clip, ClipComment $comment): void
    {
        $this->rewardOnce($user, $clip, 'comment', subjectId: (string) $comment->id);
        $this->missionEngine->recordEvent($user, 'clip.comment');
        $this->achievementService->sync($user);
    }

    private function rewardOnce(User $user, Clip $clip, string $action, ?string $subjectId = null): void
    {
        $dedupeKey = 'clips.'.$action.'.'.$user->id.'.'.$clip->id;
        $dailyLimit = (int) config('community.clips.daily_limits.'.$action, 0);

        if ($this->rewardGrantService->wasGranted($dedupeKey)) {
            return;
        }

        if ($dailyLimit > 0 && $this->rewardGrantService->countForDay($user, 'clips', $action) >= $dailyLimit) {
            return;
        }

        $rewards = $this->eventService->applyModifiers(
            (array) config('community.clips.rewards.'.$action, []),
            'bonus_clips',
        );

        $this->rewardGrantService->grant(
            user: $user,
            domain: 'clips',
            action: $action,
            dedupeKey: $dedupeKey,
            rewards: $rewards,
            subjectType: Clip::class,
            subjectId: $subjectId ?? (string) $clip->id,
        );
    }
}
