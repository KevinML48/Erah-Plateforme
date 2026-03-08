<?php

namespace App\Application\Actions\Matches;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Domain\Betting\Support\MatchOutcomeResolver;
use App\Models\EsportMatch;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SetMatchResultAction
{
    public function __construct(
        private readonly StoreAuditLogAction $storeAuditLogAction,
        private readonly MatchOutcomeResolver $matchOutcomeResolver
    ) {
    }

    public function execute(
        User $actor,
        EsportMatch $match,
        string $result,
        ?int $teamAScore = null,
        ?int $teamBScore = null
    ): EsportMatch
    {
        return DB::transaction(function () use ($actor, $match, $result, $teamAScore, $teamBScore) {
            $lockedMatch = EsportMatch::query()
                ->whereKey($match->id)
                ->lockForUpdate()
                ->with(['markets' => fn ($query) => $query->where('is_active', true)->with('selections')])
                ->firstOrFail();

            if ($lockedMatch->status === EsportMatch::STATUS_CANCELLED) {
                throw new RuntimeException('Cancelled match cannot receive a result.');
            }

            if ($lockedMatch->settled_at) {
                throw new RuntimeException('Settled match result cannot be changed.');
            }

            $resolved = $this->matchOutcomeResolver->resolve(
                $lockedMatch,
                $result,
                $teamAScore,
                $teamBScore,
                false
            );

            $lockedMatch->result = $resolved['stored_result'];
            $lockedMatch->finished_at = $lockedMatch->finished_at ?? now();
            $lockedMatch->team_a_score = $resolved['team_a_score'];
            $lockedMatch->team_b_score = $resolved['team_b_score'];
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
                    'result' => $resolved['stored_result'],
                    'team_a_score' => $resolved['team_a_score'],
                    'team_b_score' => $resolved['team_b_score'],
                ],
            );

            return $lockedMatch->fresh();
        });
    }
}
