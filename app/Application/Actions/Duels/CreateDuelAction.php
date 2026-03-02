<?php

namespace App\Application\Actions\Duels;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Notifications\NotifyAction;
use App\Jobs\ExpireDuelJob;
use App\Models\Duel;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CreateDuelAction
{
    public function __construct(
        private readonly RecordDuelEventAction $recordDuelEventAction,
        private readonly NotifyAction $notifyAction,
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    /**
     * @return array{duel: Duel, idempotent: bool}
     */
    public function execute(
        User $challenger,
        int $challengedUserId,
        string $idempotencyKey,
        ?string $message = null,
        int $expiresInMinutes = 60
    ): array {
        if ($challenger->id === $challengedUserId) {
            throw new RuntimeException('Cannot challenge yourself.');
        }

        if ($expiresInMinutes < 1 || $expiresInMinutes > 10080) {
            throw new RuntimeException('Invalid expires_in_minutes value.');
        }

        try {
            return DB::transaction(function () use (
                $challenger,
                $challengedUserId,
                $idempotencyKey,
                $message,
                $expiresInMinutes
            ) {
                $challenged = User::query()->find($challengedUserId);
                if (! $challenged) {
                    throw new ModelNotFoundException('Challenged user not found.');
                }

                $existing = Duel::query()
                    ->where('challenger_id', $challenger->id)
                    ->where('idempotency_key', $idempotencyKey)
                    ->lockForUpdate()
                    ->first();

                if ($existing) {
                    return [
                        'duel' => $existing->fresh(['challenger:id,name', 'challenged:id,name']),
                        'idempotent' => true,
                    ];
                }

                $requestedAt = now();
                $expiresAt = $requestedAt->copy()->addMinutes($expiresInMinutes);

                $duel = Duel::query()->create([
                    'challenger_id' => $challenger->id,
                    'challenged_id' => $challenged->id,
                    'status' => Duel::STATUS_PENDING,
                    'idempotency_key' => $idempotencyKey,
                    'message' => $message,
                    'requested_at' => $requestedAt,
                    'expires_at' => $expiresAt,
                    'responded_at' => null,
                    'accepted_at' => null,
                    'refused_at' => null,
                    'expired_at' => null,
                ]);

                $this->recordDuelEventAction->execute(
                    duel: $duel,
                    eventType: 'created',
                    actor: $challenger,
                    meta: [
                        'challenger_id' => $challenger->id,
                        'challenged_id' => $challenged->id,
                        'expires_at' => $expiresAt->toIso8601String(),
                    ],
                );

                $this->storeAuditLogAction->execute(
                    action: 'duels.created',
                    actor: $challenger,
                    target: $duel,
                    context: [
                        'duel_id' => $duel->id,
                        'challenger_id' => $challenger->id,
                        'challenged_id' => $challenged->id,
                        'idempotency_key' => $idempotencyKey,
                    ],
                );

                $this->notifyAction->execute(
                    user: $challenged,
                    category: 'duel',
                    message: 'Nouveau duel recu de '.$challenger->name.'.',
                    title: 'Invitation duel',
                    data: [
                        'duel_id' => $duel->id,
                        'challenger_id' => $challenger->id,
                        'challenger_name' => $challenger->name,
                        'expires_at' => $expiresAt->toIso8601String(),
                    ],
                );

                DB::afterCommit(function () use ($duel, $expiresAt): void {
                    ExpireDuelJob::dispatch($duel->id)->delay($expiresAt);
                });

                return [
                    'duel' => $duel->fresh(['challenger:id,name', 'challenged:id,name']),
                    'idempotent' => false,
                ];
            });
        } catch (QueryException $exception) {
            $messageText = $exception->getMessage();
            $isIdempotenceCollision = str_contains($messageText, 'duels_idempotency_unique')
                || str_contains($messageText, 'UNIQUE constraint failed: duels.challenger_id, duels.idempotency_key');

            if (! $isIdempotenceCollision) {
                throw $exception;
            }

            $existing = Duel::query()
                ->where('challenger_id', $challenger->id)
                ->where('idempotency_key', $idempotencyKey)
                ->firstOrFail();

            return [
                'duel' => $existing->fresh(['challenger:id,name', 'challenged:id,name']),
                'idempotent' => true,
            ];
        }
    }
}
