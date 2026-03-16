<?php

namespace App\Services;

use App\Application\Actions\Ranking\AddPointsAction;
use App\Domain\Ranking\DataTransferObjects\AddPointsResult;
use App\Models\PointsTransaction;
use App\Models\User;

class ExperienceService
{
    public function __construct(
        private readonly AddPointsAction $addPointsAction,
        private readonly RankService $rankService
    ) {
    }

    public function award(
        User $user,
        int $xp,
        string $sourceType,
        string $sourceId,
        ?User $actor = null,
        array $meta = []
    ): AddPointsResult {
        return $this->addPointsAction->execute(
            user: $user,
            kind: PointsTransaction::KIND_XP,
            points: $xp,
            sourceType: $sourceType,
            sourceId: $sourceId,
            actor: $actor,
            meta: $meta,
        );
    }

    /**
     * @return array{
     *     total_xp: int,
     *     level: int,
     *     next_level: int|null,
     *     level_start_xp: int,
     *     level_end_xp: int,
     *     current_level_threshold: int,
     *     next_level_threshold: int,
     *     xp_into_level: int,
     *     xp_for_next_level: int,
     *     xp_remaining_to_next_level: int,
     *     progress_percent: int,
     *     is_max_level: bool,
     *     rank: array{key: string, name: string, xp_threshold: int}
     * }
     */
    public function summaryFor(User $user): array
    {
        $totalXp = (int) ($user->progress?->total_xp ?? 0);
        $level = $this->levelForXp($totalXp);
        $isMaxLevel = $level >= $this->maxLevel();
        $levelStartXp = $this->xpRequiredForLevel($level);
        $levelEndXp = $isMaxLevel
            ? $levelStartXp
            : $this->xpRequiredForLevel($level + 1);
        $xpIntoLevel = max(0, $totalXp - $levelStartXp);
        $xpForNextLevel = $isMaxLevel
            ? max(1, $xpIntoLevel)
            : max(1, $levelEndXp - $levelStartXp);
        $xpRemainingToNextLevel = $isMaxLevel
            ? 0
            : max(0, $levelEndXp - $totalXp);

        return [
            'total_xp' => $totalXp,
            'level' => $level,
            'next_level' => $isMaxLevel ? null : $level + 1,
            'level_start_xp' => $levelStartXp,
            'level_end_xp' => $levelEndXp,
            'current_level_threshold' => $levelStartXp,
            'next_level_threshold' => $levelEndXp,
            'xp_into_level' => $xpIntoLevel,
            'xp_for_next_level' => $xpForNextLevel,
            'xp_remaining_to_next_level' => $xpRemainingToNextLevel,
            'progress_percent' => $isMaxLevel
                ? 100
                : (int) min(100, round(($xpIntoLevel / $xpForNextLevel) * 100)),
            'is_max_level' => $isMaxLevel,
            'rank' => $this->rankService->resolveLeague($totalXp),
        ];
    }

    public function levelForXp(int $xp): int
    {
        $level = 1;

        while ($level < $this->maxLevel() && $xp >= $this->xpRequiredForLevel($level + 1)) {
            $level++;
        }

        return $level;
    }

    public function xpRequiredForLevel(int $level): int
    {
        $level = max(1, min($level, $this->maxLevel() + 1));
        $curve = (array) config('community.progression.level_curve', []);
        $baseXp = max(50, (int) ($curve['base_xp'] ?? 250));
        $growth = max(0, (int) ($curve['growth_per_level'] ?? 75));

        if ($level <= 1) {
            return 0;
        }

        $steps = $level - 1;

        return (int) (($steps * $baseXp) + (($steps * ($steps - 1)) / 2) * $growth);
    }

    private function maxLevel(): int
    {
        return max(10, (int) config('community.progression.level_curve.max_level', 200));
    }
}
