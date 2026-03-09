<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LeaderboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommunityLeaderboardController extends Controller
{
    public function __invoke(Request $request, LeaderboardService $leaderboardService): JsonResponse
    {
        $validated = $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $limit = (int) ($validated['limit'] ?? 20);

        return response()->json([
            'xp' => $leaderboardService->xp($limit),
            'rank' => $leaderboardService->byRank($limit),
            'duel' => $leaderboardService->duel($limit),
        ]);
    }
}
