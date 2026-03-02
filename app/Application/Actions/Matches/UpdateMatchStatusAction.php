<?php

namespace App\Application\Actions\Matches;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\EsportMatch;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class UpdateMatchStatusAction
{
    public function __construct(
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    public function execute(User $actor, EsportMatch $match, string $status): EsportMatch
    {
        return DB::transaction(function () use ($actor, $match, $status) {
            $lockedMatch = EsportMatch::query()
                ->whereKey($match->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! in_array($status, EsportMatch::statuses(), true)) {
                throw new RuntimeException('Invalid match status.');
            }

            if ($lockedMatch->settled_at && $status !== $lockedMatch->status) {
                throw new RuntimeException('Settled match status cannot be changed.');
            }

            $lockedMatch->status = $status;
            $lockedMatch->updated_by = $actor->id;

            if ($status === EsportMatch::STATUS_FINISHED && ! $lockedMatch->finished_at) {
                $lockedMatch->finished_at = now();
            }

            $lockedMatch->save();

            $this->storeAuditLogAction->execute(
                action: 'matches.status.updated',
                actor: $actor,
                target: $lockedMatch,
                context: [
                    'match_id' => $lockedMatch->id,
                    'previous_status' => $match->status,
                    'new_status' => $status,
                ],
            );

            return $lockedMatch->fresh();
        });
    }
}
