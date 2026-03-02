<?php

namespace App\Http\Controllers\Api;

use App\Application\Actions\Ranking\EnsureUserProgressAction;
use App\Domain\Ranking\Queries\LeaderboardQuery;
use App\Http\Controllers\Controller;
use App\Models\League;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function meProgress(Request $request, EnsureUserProgressAction $ensureUserProgressAction): JsonResponse
    {
        $user = $request->user();
        $progress = $ensureUserProgressAction->execute($user)->load('league');

        return response()->json([
            'user_id' => $user->id,
            'league' => [
                'id' => $progress->league?->id,
                'key' => $progress->league?->key,
                'name' => $progress->league?->name,
            ],
            'total_xp' => $progress->total_xp,
            'total_rank_points' => $progress->total_rank_points,
            'last_points_at' => $progress->last_points_at,
        ]);
    }

    public function leagues(): JsonResponse
    {
        $leagues = League::query()
            ->active()
            ->orderBy('sort_order')
            ->get(['id', 'key', 'name', 'min_rank_points', 'sort_order']);

        return response()->json([
            'data' => $leagues,
        ]);
    }

    public function leaderboard(
        Request $request,
        string $key,
        LeaderboardQuery $leaderboardQuery
    ): JsonResponse {
        $validated = $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        try {
            $payload = $leaderboardQuery->execute($key, (int) ($validated['limit'] ?? 50));
        } catch (ModelNotFoundException) {
            return response()->json([
                'message' => 'League not found.',
            ], 404);
        }

        return response()->json($payload);
    }
}
