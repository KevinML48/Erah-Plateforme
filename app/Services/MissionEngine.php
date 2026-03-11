<?php

namespace App\Services;

use App\Application\Actions\Rewards\EnsureCurrentMissionInstancesAction;
use App\Models\User;
use App\Models\UserMission;
use Illuminate\Support\Collection;

class MissionEngine
{
    public function __construct(
        private readonly EnsureCurrentMissionInstancesAction $ensureCurrentMissionInstancesAction,
        private readonly MissionTrackingService $missionTrackingService,
    ) {
    }

    /**
     * @return array{daily: int, weekly: int, monthly: int, once: int, event_window: int}
     */
    public function ensureCurrent(User $user): array
    {
        return $this->ensureCurrentMissionInstancesAction->execute($user);
    }

    /**
     * @return Collection<int, UserMission>
     */
    public function recordEvent(User $user, string $eventType, int $amount = 1, array $context = []): Collection
    {
        $eventKey = isset($context['event_key']) ? (string) $context['event_key'] : null;
        $subjectType = isset($context['subject_type']) ? (string) $context['subject_type'] : null;
        $subjectId = isset($context['subject_id']) ? (string) $context['subject_id'] : null;

        return $this->missionTrackingService->record(
            user: $user,
            eventType: $eventType,
            amount: $amount,
            context: $context,
            eventKey: $eventKey,
            subjectType: $subjectType,
            subjectId: $subjectId,
        );
    }
}
