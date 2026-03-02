<?php

namespace App\Http\Controllers\Api\Admin;

use App\Application\Actions\Ranking\AddPointsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\GrantPointsRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class PointsGrantController extends Controller
{
    public function __invoke(GrantPointsRequest $request, AddPointsAction $addPointsAction): JsonResponse
    {
        $validated = $request->validated();
        $targetUser = User::query()->findOrFail($validated['user_id']);

        try {
            $result = $addPointsAction->execute(
                user: $targetUser,
                kind: $validated['kind'],
                points: (int) $validated['points'],
                sourceType: 'admin_grant',
                sourceId: $validated['idempotency_key'],
                actor: $request->user(),
                meta: [
                    'reason' => $validated['reason'] ?? null,
                    'meta' => $validated['meta'] ?? null,
                ],
            );
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'idempotent' => $result->idempotent,
            'transaction' => [
                'id' => $result->transaction->id,
                'user_id' => $result->transaction->user_id,
                'kind' => $result->transaction->kind,
                'points' => $result->transaction->points,
                'source_type' => $result->transaction->source_type,
                'source_id' => $result->transaction->source_id,
                'created_at' => $result->transaction->created_at,
            ],
            'progress' => [
                'league' => [
                    'id' => $result->progress->league?->id,
                    'key' => $result->progress->league?->key,
                    'name' => $result->progress->league?->name,
                ],
                'total_xp' => $result->progress->total_xp,
                'total_rank_points' => $result->progress->total_rank_points,
            ],
            'promotions' => $result->promotions->map(function ($promotion) {
                return [
                    'id' => $promotion->id,
                    'from_league_id' => $promotion->from_league_id,
                    'to_league_id' => $promotion->to_league_id,
                    'rank_points' => $promotion->rank_points,
                    'promoted_at' => $promotion->promoted_at,
                ];
            })->values(),
        ]);
    }
}
