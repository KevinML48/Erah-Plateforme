<?php

namespace App\Application\Actions\Matches;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\EsportMatch;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class UnlockTournamentChildMatchesAction
{
    public function __construct(
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    public function execute(User $actor, EsportMatch $match): EsportMatch
    {
        return DB::transaction(function () use ($actor, $match) {
            $lockedMatch = EsportMatch::query()
                ->whereKey($match->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedMatch->isTournamentRun()) {
                throw new RuntimeException('Only tournament events can unlock child matches.');
            }

            if ($lockedMatch->game_key !== EsportMatch::GAME_ROCKET_LEAGUE) {
                throw new RuntimeException('Child match unlocking is currently reserved for Rocket League tournaments.');
            }

            if ($lockedMatch->child_matches_unlocked_at === null) {
                $lockedMatch->child_matches_unlocked_at = now();
                $lockedMatch->updated_by = $actor->id;
                $lockedMatch->save();

                $this->storeAuditLogAction->execute(
                    action: 'matches.children.unlocked',
                    actor: $actor,
                    target: $lockedMatch,
                    context: [
                        'match_id' => $lockedMatch->id,
                        'match_key' => $lockedMatch->match_key,
                    ],
                );
            }

            return $lockedMatch->fresh();
        });
    }
}
