<?php

namespace App\Application\Actions\Matches;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\EsportMatch;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class UpdateMatchAction
{
    public function __construct(
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function execute(User $actor, EsportMatch $match, array $payload): EsportMatch
    {
        return DB::transaction(function () use ($actor, $match, $payload) {
            $lockedMatch = EsportMatch::query()
                ->whereKey($match->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (in_array($lockedMatch->status, [
                EsportMatch::STATUS_LIVE,
                EsportMatch::STATUS_FINISHED,
                EsportMatch::STATUS_SETTLED,
                EsportMatch::STATUS_CANCELLED,
            ], true)) {
                throw new RuntimeException('Only scheduled or locked matches can be edited.');
            }

            $startsAt = \Illuminate\Support\Carbon::parse((string) $payload['starts_at']);
            $homeTeam = (string) ($payload['home_team'] ?? $payload['team_a_name']);
            $awayTeam = (string) ($payload['away_team'] ?? $payload['team_b_name']);
            $lockedAt = ! empty($payload['locked_at'])
                ? \Illuminate\Support\Carbon::parse((string) $payload['locked_at'])
                : $startsAt->copy()->subMinutes((int) config('betting.match.default_lock_offset_minutes', 5));

            $lockedMatch->fill([
                'game_key' => $payload['game_key'] ?? null,
                'team_a_name' => $homeTeam,
                'team_b_name' => $awayTeam,
                'home_team' => $homeTeam,
                'away_team' => $awayTeam,
                'starts_at' => $startsAt,
                'locked_at' => $lockedAt,
                'updated_by' => $actor->id,
            ]);
            $lockedMatch->save();

            $this->storeAuditLogAction->execute(
                action: 'matches.updated',
                actor: $actor,
                target: $lockedMatch,
                context: [
                    'match_id' => $lockedMatch->id,
                    'match_key' => $lockedMatch->match_key,
                    'status' => $lockedMatch->status,
                ],
            );

            return $lockedMatch->fresh();
        });
    }
}
