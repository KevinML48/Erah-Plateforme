<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserMission;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class MissionClaimService
{
    public function __construct(
        private readonly RewardGrantService $rewardGrantService
    ) {
    }

    public function claim(User $user, UserMission $mission): UserMission
    {
        return DB::transaction(function () use ($user, $mission) {
            $mission = UserMission::query()
                ->whereKey($mission->id)
                ->with('instance.template')
                ->lockForUpdate()
                ->firstOrFail();

            if ((int) $mission->user_id !== (int) $user->id) {
                throw new AuthorizationException('Vous ne pouvez pas reclamer cette mission.');
            }

            $template = $mission->instance?->template;
            if (! $template) {
                throw new RuntimeException('Template mission introuvable.');
            }

            if (! $template->requires_claim) {
                throw new RuntimeException('Cette mission ne demande pas de reclamation manuelle.');
            }

            if ($mission->completed_at === null) {
                throw new RuntimeException('La mission doit etre terminee avant de reclamer la recompense.');
            }

            if ($mission->isExpired()) {
                throw new RuntimeException('La mission a expire avant la reclamation.');
            }

            if ($mission->claimed_at !== null) {
                return $mission;
            }

            $this->rewardGrantService->grant(
                user: $user,
                domain: 'missions',
                action: 'completion',
                dedupeKey: 'mission.completion.'.$mission->id,
                rewards: $template->normalizedRewards(),
                subjectType: UserMission::class,
                subjectId: (string) $mission->id,
                meta: [
                    'event_type' => $template->normalizedEventType(),
                    'mission_template_key' => $template->key,
                    'claimed_manually' => true,
                ],
            );

            $mission->rewarded_at = $mission->rewarded_at ?: now();
            $mission->claimed_at = now();
            $mission->save();

            return $mission->fresh(['instance.template', 'completion']);
        });
    }
}
