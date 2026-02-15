<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\MissionProgress;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class MissionStatsService
{
    public function countClaimsByMission(int $missionId): int
    {
        return MissionProgress::query()->where('mission_id', $missionId)->count();
    }

    public function topMissionsByClaims(?CarbonInterface $from = null, ?CarbonInterface $to = null, int $limit = 10): Collection
    {
        return MissionProgress::query()
            ->selectRaw('mission_id, COUNT(*) as claims_count')
            ->when($from !== null, fn ($query) => $query->where('created_at', '>=', $from))
            ->when($to !== null, fn ($query) => $query->where('created_at', '<=', $to))
            ->groupBy('mission_id')
            ->orderByDesc('claims_count')
            ->limit($limit)
            ->get();
    }
}
