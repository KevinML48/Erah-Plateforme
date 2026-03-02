<?php

namespace App\Http\Controllers\Api;

use App\Application\Actions\Duels\AcceptDuelAction;
use App\Application\Actions\Duels\CreateDuelAction;
use App\Application\Actions\Duels\RefuseDuelAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateDuelRequest;
use App\Models\Duel;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RuntimeException;

class DuelController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', 'string', Rule::in(Duel::statuses())],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $limit = (int) ($validated['limit'] ?? 20);

        $query = Duel::query()
            ->forUser($request->user()->id)
            ->with(['challenger:id,name', 'challenged:id,name'])
            ->orderByDesc('id');

        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        $rows = $query->limit($limit)->get();

        return response()->json([
            'data' => $rows->map(fn (Duel $duel) => $this->mapDuel($duel))->values(),
        ]);
    }

    public function store(CreateDuelRequest $request, CreateDuelAction $createDuelAction): JsonResponse
    {
        $validated = $request->validated();

        try {
            $result = $createDuelAction->execute(
                challenger: $request->user(),
                challengedUserId: (int) $validated['challenged_user_id'],
                idempotencyKey: $validated['idempotency_key'],
                message: $validated['message'] ?? null,
                expiresInMinutes: (int) ($validated['expires_in_minutes'] ?? 60),
            );
        } catch (ModelNotFoundException) {
            return response()->json([
                'message' => 'Challenged user not found.',
            ], 404);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'idempotent' => $result['idempotent'],
            'data' => $this->mapDuel($result['duel']),
        ], $result['idempotent'] ? 200 : 201);
    }

    public function accept(Request $request, int $id, AcceptDuelAction $acceptDuelAction): JsonResponse
    {
        try {
            $result = $acceptDuelAction->execute($request->user(), $id);
        } catch (ModelNotFoundException) {
            return response()->json([
                'message' => 'Duel not found.',
            ], 404);
        } catch (AuthorizationException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 403);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'idempotent' => $result['idempotent'],
            'data' => $this->mapDuel($result['duel']),
        ]);
    }

    public function refuse(Request $request, int $id, RefuseDuelAction $refuseDuelAction): JsonResponse
    {
        try {
            $result = $refuseDuelAction->execute($request->user(), $id);
        } catch (ModelNotFoundException) {
            return response()->json([
                'message' => 'Duel not found.',
            ], 404);
        } catch (AuthorizationException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 403);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'idempotent' => $result['idempotent'],
            'data' => $this->mapDuel($result['duel']),
        ]);
    }

    private function mapDuel(Duel $duel): array
    {
        return [
            'id' => $duel->id,
            'status' => $duel->status,
            'idempotency_key' => $duel->idempotency_key,
            'message' => $duel->message,
            'challenger' => [
                'id' => $duel->challenger?->id,
                'name' => $duel->challenger?->name,
            ],
            'challenged' => [
                'id' => $duel->challenged?->id,
                'name' => $duel->challenged?->name,
            ],
            'requested_at' => $duel->requested_at,
            'expires_at' => $duel->expires_at,
            'responded_at' => $duel->responded_at,
            'accepted_at' => $duel->accepted_at,
            'refused_at' => $duel->refused_at,
            'expired_at' => $duel->expired_at,
            'created_at' => $duel->created_at,
            'updated_at' => $duel->updated_at,
        ];
    }
}
