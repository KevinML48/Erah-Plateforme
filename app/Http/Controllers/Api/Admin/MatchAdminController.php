<?php

namespace App\Http\Controllers\Api\Admin;

use App\Application\Actions\Bets\SettleMatchBetsAction;
use App\Application\Actions\Matches\CreateMatchAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\SettleMatchRequest;
use App\Http\Requests\Api\Admin\StoreMatchRequest;
use App\Models\EsportMatch;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class MatchAdminController extends Controller
{
    public function store(StoreMatchRequest $request, CreateMatchAction $createMatchAction): JsonResponse
    {
        $match = $createMatchAction->execute(
            actor: $request->user(),
            payload: $request->validated(),
        );

        return response()->json([
            'data' => $this->mapMatch($match),
        ], 201);
    }

    public function settle(
        SettleMatchRequest $request,
        int $id,
        SettleMatchBetsAction $settleMatchBetsAction
    ): JsonResponse {
        $validated = $request->validated();

        try {
            $result = $settleMatchBetsAction->execute(
                actor: $request->user(),
                matchId: $id,
                result: $validated['result'],
                idempotencyKey: $validated['idempotency_key'],
            );
        } catch (ModelNotFoundException) {
            return response()->json([
                'message' => 'Match not found.',
            ], 404);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'idempotent' => $result['idempotent'],
            'match' => $this->mapMatch($result['match']),
            'settlement' => [
                'id' => $result['settlement']->id,
                'idempotency_key' => $result['settlement']->idempotency_key,
                'result' => $result['settlement']->result,
                'bets_total' => $result['settlement']->bets_total,
                'won_count' => $result['settlement']->won_count,
                'lost_count' => $result['settlement']->lost_count,
                'void_count' => $result['settlement']->void_count,
                'payout_total' => $result['settlement']->payout_total,
                'processed_by' => $result['settlement']->processed_by,
                'processed_at' => $result['settlement']->processed_at,
            ],
        ]);
    }

    private function mapMatch(EsportMatch $match): array
    {
        return [
            'id' => $match->id,
            'match_key' => $match->match_key,
            'home_team' => $match->home_team,
            'away_team' => $match->away_team,
            'starts_at' => $match->starts_at,
            'status' => $match->status,
            'result' => $match->result,
            'settled_at' => $match->settled_at,
            'created_by' => $match->created_by,
            'updated_by' => $match->updated_by,
            'created_at' => $match->created_at,
            'updated_at' => $match->updated_at,
        ];
    }
}
