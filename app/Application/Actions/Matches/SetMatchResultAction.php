<?php

namespace App\Application\Actions\Matches;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\EsportMatch;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SetMatchResultAction
{
    public function __construct(
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    public function execute(User $actor, EsportMatch $match, string $result): EsportMatch
    {
        return DB::transaction(function () use ($actor, $match, $result) {
            $lockedMatch = EsportMatch::query()
                ->whereKey($match->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedMatch->status === EsportMatch::STATUS_CANCELLED) {
                throw new RuntimeException('Cancelled match cannot receive a result.');
            }

            if ($lockedMatch->settled_at) {
                throw new RuntimeException('Settled match result cannot be changed.');
            }

            $normalizedResult = EsportMatch::normalizeResultKey($result);
            if (! $normalizedResult) {
                throw new RuntimeException('Invalid match result.');
            }

            $lockedMatch->result = $normalizedResult;
            $lockedMatch->finished_at = $lockedMatch->finished_at ?? now();
            $lockedMatch->status = in_array($lockedMatch->status, [EsportMatch::STATUS_FINISHED, EsportMatch::STATUS_SETTLED], true)
                ? $lockedMatch->status
                : EsportMatch::STATUS_FINISHED;
            $lockedMatch->updated_by = $actor->id;
            $lockedMatch->save();

            $this->storeAuditLogAction->execute(
                action: 'matches.result.updated',
                actor: $actor,
                target: $lockedMatch,
                context: [
                    'match_id' => $lockedMatch->id,
                    'result' => $normalizedResult,
                ],
            );

            return $lockedMatch->fresh();
        });
    }
}
