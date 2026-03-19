<?php

namespace App\Application\Actions\Duels;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Notifications\NotifyAction;
use App\Models\Duel;
use Illuminate\Support\Facades\DB;

class ExpireDuelAction
{
    public function __construct(
        private readonly RecordDuelEventAction $recordDuelEventAction,
        private readonly NotifyAction $notifyAction,
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    /**
     * @return array{duel: Duel|null, idempotent: bool}
     */
    public function execute(int $duelId): array
    {
        return DB::transaction(function () use ($duelId) {
            $duel = Duel::query()
                ->whereKey($duelId)
                ->lockForUpdate()
                ->first();

            if (! $duel) {
                return [
                    'duel' => null,
                    'idempotent' => true,
                ];
            }

            return $this->executeForLockedDuel($duel);
        });
    }

    /**
     * @return array{duel: Duel, idempotent: bool}
     */
    public function executeForLockedDuel(Duel $duel): array
    {
        if ($duel->status !== Duel::STATUS_PENDING) {
            return [
                'duel' => $duel->fresh(['challenger:id,name', 'challenged:id,name']),
                'idempotent' => true,
            ];
        }

        if ($duel->expires_at && now()->lessThan($duel->expires_at)) {
            return [
                'duel' => $duel->fresh(['challenger:id,name', 'challenged:id,name']),
                'idempotent' => true,
            ];
        }

        $duel->status = Duel::STATUS_EXPIRED;
        $duel->responded_at = now();
        $duel->accepted_at = null;
        $duel->refused_at = null;
        $duel->expired_at = now();
        $duel->save();

        $this->recordDuelEventAction->execute(
            duel: $duel,
            eventType: 'expired',
            actor: null,
            meta: [
                'duel_id' => $duel->id,
                'challenger_id' => $duel->challenger_id,
                'challenged_id' => $duel->challenged_id,
            ],
        );

        $this->storeAuditLogAction->execute(
            action: 'duels.expired',
            actor: null,
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
                message: 'Votre duel a expire sans reponse.',
                title: 'Duel expire',
                data: [
                    'duel_id' => $duel->id,
                    'challenged_id' => $duel->challenged_id,
                ],
            );
        }

        return [
            'duel' => $duel->fresh(['challenger:id,name', 'challenged:id,name']),
            'idempotent' => false,
        ];
    }
}
