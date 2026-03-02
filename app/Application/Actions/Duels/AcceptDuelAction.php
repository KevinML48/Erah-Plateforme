<?php

namespace App\Application\Actions\Duels;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Notifications\NotifyAction;
use App\Models\Duel;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AcceptDuelAction
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
    public function execute(User $actor, int $duelId): array
    {
        return DB::transaction(function () use ($actor, $duelId) {
            $duel = Duel::query()
                ->whereKey($duelId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($duel->challenged_id !== $actor->id) {
                throw new AuthorizationException('Only challenged user can accept this duel.');
            }

            if ($duel->status === Duel::STATUS_ACCEPTED) {
                return [
                    'duel' => $duel->fresh(['challenger:id,name', 'challenged:id,name']),
                    'idempotent' => true,
                ];
            }

            if ($duel->status === Duel::STATUS_REFUSED) {
                throw new RuntimeException('Duel already refused.');
            }

            if ($duel->status === Duel::STATUS_EXPIRED) {
                throw new RuntimeException('Duel already expired.');
            }

            if ($duel->status !== Duel::STATUS_PENDING) {
                throw new RuntimeException('Duel cannot be accepted.');
            }

            if ($duel->expires_at && now()->greaterThan($duel->expires_at)) {
                throw new RuntimeException('Duel expired.');
            }

            $duel->status = Duel::STATUS_ACCEPTED;
            $duel->responded_at = now();
            $duel->accepted_at = now();
            $duel->refused_at = null;
            $duel->expired_at = null;
            $duel->save();

            $this->recordDuelEventAction->execute(
                duel: $duel,
                eventType: 'accepted',
                actor: $actor,
                meta: [
                    'duel_id' => $duel->id,
                    'challenger_id' => $duel->challenger_id,
                    'challenged_id' => $duel->challenged_id,
                ],
            );

            $this->storeAuditLogAction->execute(
                action: 'duels.accepted',
                actor: $actor,
                target: $duel,
                context: [
                    'duel_id' => $duel->id,
                    'challenger_id' => $duel->challenger_id,
                    'challenged_id' => $duel->challenged_id,
                ],
            );

            $challenger = $duel->challenger()->first();
            if ($challenger) {
                $this->notifyAction->execute(
                    user: $challenger,
                    category: 'duel',
                    message: $actor->name.' a accepte votre duel.',
                    title: 'Duel accepte',
                    data: [
                        'duel_id' => $duel->id,
                        'challenged_id' => $actor->id,
                        'challenged_name' => $actor->name,
                    ],
                );
            }

            return [
                'duel' => $duel->fresh(['challenger:id,name', 'challenged:id,name']),
                'idempotent' => false,
            ];
        });
    }
}
