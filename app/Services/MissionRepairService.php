<?php

namespace App\Services;

use App\Application\Actions\Rewards\EnsureCurrentMissionInstancesAction;
use App\Models\MissionCompletion;
use App\Models\User;
use App\Models\UserMission;

class MissionRepairService
{
    public function __construct(
        private readonly EnsureCurrentMissionInstancesAction $ensureCurrentMissionInstancesAction,
        private readonly RewardGrantService $rewardGrantService,
    ) {
    }

    /**
     * @return array{users_scanned: int, missions_scanned: int, completions_created: int, rewards_repaired: int, claimables_normalized: int, already_ok: int}
     */
    public function repair(?int $userId = null, bool $dryRun = false, int $chunk = 100): array
    {
        $stats = [
            'users_scanned' => 0,
            'missions_scanned' => 0,
            'completions_created' => 0,
            'rewards_repaired' => 0,
            'claimables_normalized' => 0,
            'already_ok' => 0,
        ];

        User::query()
            ->when($userId !== null, fn ($query) => $query->whereKey($userId))
            ->orderBy('id')
            ->chunkById($chunk, function ($users) use (&$stats, $dryRun): void {
                foreach ($users as $user) {
                    $stats['users_scanned']++;

                    if (! $dryRun) {
                        $this->ensureCurrentMissionInstancesAction->execute($user);
                    }

                    $missions = UserMission::query()
                        ->where('user_id', $user->id)
                        ->whereNotNull('completed_at')
                        ->with(['instance.template', 'completion'])
                        ->get();

                    foreach ($missions as $mission) {
                        $stats['missions_scanned']++;
                        $template = $mission->instance?->template;

                        if (! $template) {
                            continue;
                        }

                        if (! $mission->completion) {
                            $stats['completions_created']++;

                            if (! $dryRun) {
                                MissionCompletion::query()->firstOrCreate(
                                    [
                                        'user_id' => $user->id,
                                        'user_mission_id' => $mission->id,
                                    ],
                                    [
                                        'completed_at' => $mission->completed_at,
                                        'created_at' => now(),
                                    ],
                                );
                            }
                        }

                        $dedupeKey = 'mission.completion.'.$mission->id;
                        $grantExists = $this->rewardGrantService->wasGranted($dedupeKey);

                        if ($template->requires_claim) {
                            if ($mission->claimed_at === null) {
                                if ($mission->rewarded_at !== null) {
                                    $stats['claimables_normalized']++;

                                    if (! $dryRun) {
                                        $mission->rewarded_at = null;
                                        $mission->save();
                                    }
                                } else {
                                    $stats['already_ok']++;
                                }

                                continue;
                            }

                            if ($grantExists && $mission->rewarded_at !== null) {
                                $stats['already_ok']++;
                                continue;
                            }

                            $stats['rewards_repaired']++;

                            if (! $dryRun) {
                                if (! $grantExists) {
                                    $this->rewardGrantService->grant(
                                        user: $user,
                                        domain: 'missions',
                                        action: 'completion',
                                        dedupeKey: $dedupeKey,
                                        rewards: $template->normalizedRewards(),
                                        subjectType: UserMission::class,
                                        subjectId: (string) $mission->id,
                                        meta: ['mission_template_key' => $template->key],
                                    );
                                }

                                $mission->rewarded_at = $mission->rewarded_at ?: $mission->claimed_at ?: now();
                                $mission->save();
                            }

                            continue;
                        }

                        if ($grantExists && $mission->rewarded_at !== null && $mission->claimed_at !== null) {
                            $stats['already_ok']++;
                            continue;
                        }

                        $stats['rewards_repaired']++;

                        if (! $dryRun) {
                            if (! $grantExists) {
                                $this->rewardGrantService->grant(
                                    user: $user,
                                    domain: 'missions',
                                    action: 'completion',
                                    dedupeKey: $dedupeKey,
                                    rewards: $template->normalizedRewards(),
                                    subjectType: UserMission::class,
                                    subjectId: (string) $mission->id,
                                    meta: ['mission_template_key' => $template->key],
                                );
                            }

                            $rewardedAt = $mission->rewarded_at ?: $mission->claimed_at ?: $mission->completed_at ?: now();
                            $mission->rewarded_at = $rewardedAt;
                            $mission->claimed_at = $mission->claimed_at ?: $rewardedAt;
                            $mission->save();
                        }
                    }
                }
            });

        return $stats;
    }
}