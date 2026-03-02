<?php

namespace App\Application\Actions\Matches;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\EsportMatch;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateMatchAction
{
    public function __construct(
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function execute(User $actor, array $payload): EsportMatch
    {
        return DB::transaction(function () use ($actor, $payload) {
            $startsAt = \Illuminate\Support\Carbon::parse((string) $payload['starts_at']);
            $homeTeam = (string) ($payload['home_team'] ?? $payload['team_a_name']);
            $awayTeam = (string) ($payload['away_team'] ?? $payload['team_b_name']);
            $matchKey = (string) ($payload['match_key'] ?? 'mch-'.Str::lower(Str::random(14)));
            $lockedAt = ! empty($payload['locked_at'])
                ? \Illuminate\Support\Carbon::parse((string) $payload['locked_at'])
                : $startsAt->copy()->subMinutes((int) config('betting.match.default_lock_offset_minutes', 5));

            $match = EsportMatch::query()->create([
                'match_key' => $matchKey,
                'game_key' => $payload['game_key'] ?? null,
                'team_a_name' => $homeTeam,
                'team_b_name' => $awayTeam,
                'home_team' => $homeTeam,
                'away_team' => $awayTeam,
                'starts_at' => $startsAt,
                'locked_at' => $lockedAt,
                'status' => EsportMatch::STATUS_SCHEDULED,
                'result' => null,
                'finished_at' => null,
                'settled_at' => null,
                'meta' => $payload['meta'] ?? null,
                'created_by' => $actor->id,
                'updated_by' => null,
            ]);

            $this->storeAuditLogAction->execute(
                action: 'matches.created',
                actor: $actor,
                target: $match,
                context: [
                    'match_id' => $match->id,
                    'match_key' => $match->match_key,
                    'home_team' => $match->home_team,
                    'away_team' => $match->away_team,
                    'locked_at' => (string) $match->locked_at,
                    'starts_at' => (string) $match->starts_at,
                ],
            );

            return $match->fresh();
        });
    }
}
