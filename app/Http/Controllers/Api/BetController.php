<?php

namespace App\Http\Controllers\Api;

use App\Application\Actions\Bets\PlaceBetAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlaceBetRequest;
use App\Models\Bet;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RuntimeException;

class BetController extends Controller
{
    public function store(PlaceBetRequest $request, PlaceBetAction $placeBetAction): JsonResponse
    {
        try {
            $result = $placeBetAction->execute(
                user: $request->user(),
                payload: $request->validated(),
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
            'data' => $this->mapBet($result['bet']),
        ], $result['idempotent'] ? 200 : 201);
    }

    public function myBets(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', 'string', Rule::in(Bet::statuses())],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $limit = (int) ($validated['limit'] ?? 20);

        $query = Bet::query()
            ->where('user_id', $request->user()->id)
            ->with('match:id,match_key,home_team,away_team,starts_at,status,result')
            ->orderByDesc('id');

        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        $rows = $query->limit($limit)->get();

        return response()->json([
            'data' => $rows->map(fn (Bet $bet) => $this->mapBet($bet))->values(),
        ]);
    }

    private function mapBet(Bet $bet): array
    {
        return [
            'id' => $bet->id,
            'match_id' => $bet->match_id,
            'prediction' => $bet->prediction,
            'stake_points' => $bet->stake_points,
            'potential_payout' => $bet->potential_payout,
            'settlement_points' => $bet->settlement_points,
            'status' => $bet->status,
            'idempotency_key' => $bet->idempotency_key,
            'placed_at' => $bet->placed_at,
            'settled_at' => $bet->settled_at,
            'match' => [
                'id' => $bet->match?->id,
                'match_key' => $bet->match?->match_key,
                'home_team' => $bet->match?->home_team,
                'away_team' => $bet->match?->away_team,
                'starts_at' => $bet->match?->starts_at,
                'status' => $bet->match?->status,
                'result' => $bet->match?->result,
            ],
            'created_at' => $bet->created_at,
            'updated_at' => $bet->updated_at,
        ];
    }
}
