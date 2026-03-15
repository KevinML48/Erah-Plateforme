<?php

namespace App\Application\Actions\Matches;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\EsportMatch;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class CreateMatchAction
{
    public function __construct(
        private readonly StoreAuditLogAction $storeAuditLogAction,
        private readonly SyncMatchMarketsAction $syncMatchMarketsAction
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function execute(User $actor, array $payload): EsportMatch
    {
        return DB::transaction(function () use ($actor, $payload) {
            $eventType = (string) ($payload['event_type'] ?? EsportMatch::EVENT_TYPE_HEAD_TO_HEAD);
            $startsAt = \Illuminate\Support\Carbon::parse((string) $payload['starts_at']);
            $matchKey = (string) ($payload['match_key'] ?? 'mch-'.Str::lower(Str::random(14)));
            $lockedAt = ! empty($payload['locked_at'])
                ? \Illuminate\Support\Carbon::parse((string) $payload['locked_at'])
                : $startsAt->copy()->subMinutes((int) config('betting.match.default_lock_offset_minutes', 5));
            $endsAt = ! empty($payload['ends_at'])
                ? \Illuminate\Support\Carbon::parse((string) $payload['ends_at'])
                : null;
            $parentMatch = $this->resolveParentMatch($eventType, $payload);

            [$homeTeam, $awayTeam] = $this->resolveLegacyTeams($eventType, $payload, $parentMatch);
            $teamAName = $eventType === EsportMatch::EVENT_TYPE_TOURNAMENT_RUN ? null : $homeTeam;
            $teamBName = $eventType === EsportMatch::EVENT_TYPE_TOURNAMENT_RUN ? null : $awayTeam;

            $match = EsportMatch::query()->create([
                'match_key' => $matchKey,
                'game_key' => $payload['game_key'] ?? $parentMatch?->game_key,
                'event_type' => $eventType,
                'event_name' => $this->resolveEventName($eventType, $payload, $parentMatch),
                'compétition_name' => $payload['compétition_name'] ?? $parentMatch?->compétition_name,
                'compétition_stage' => $payload['compétition_stage'] ?? $parentMatch?->compétition_stage,
                'compétition_split' => $payload['compétition_split'] ?? $parentMatch?->compétition_split,
                'best_of' => isset($payload['best_of']) && $payload['best_of'] !== '' ? (int) $payload['best_of'] : null,
                'parent_match_id' => $parentMatch?->id,
                'team_a_name' => $teamAName,
                'team_b_name' => $teamBName,
                'home_team' => $homeTeam,
                'away_team' => $awayTeam,
                'starts_at' => $startsAt,
                'locked_at' => $lockedAt,
                'ends_at' => $endsAt,
                'status' => EsportMatch::STATUS_SCHEDULED,
                'result' => null,
                'finished_at' => null,
                'team_a_score' => null,
                'team_b_score' => null,
                'child_matches_unlocked_at' => ! empty($payload['child_matches_unlocked_at'])
                    ? \Illuminate\Support\Carbon::parse((string) $payload['child_matches_unlocked_at'])
                    : null,
                'settled_at' => null,
                'meta' => $payload['meta'] ?? null,
                'created_by' => $actor->id,
                'updated_by' => null,
            ]);

            $this->syncMatchMarketsAction->execute($match, $payload['markets'] ?? null, array_merge($payload, [
                'team_a_name' => $teamAName,
                'team_b_name' => $teamBName,
                'home_team' => $homeTeam,
                'away_team' => $awayTeam,
            ]));

            $this->storeAuditLogAction->execute(
                action: 'matches.created',
                actor: $actor,
                target: $match,
                context: [
                    'match_id' => $match->id,
                    'match_key' => $match->match_key,
                    'event_type' => $match->event_type,
                    'game_key' => $match->game_key,
                    'parent_match_id' => $match->parent_match_id,
                    'home_team' => $match->home_team,
                    'away_team' => $match->away_team,
                    'locked_at' => (string) $match->locked_at,
                    'starts_at' => (string) $match->starts_at,
                ],
            );

            return $match->fresh();
        });
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function resolveParentMatch(string $eventType, array $payload): ?EsportMatch
    {
        $parentMatchId = isset($payload['parent_match_id']) && $payload['parent_match_id'] !== ''
            ? (int) $payload['parent_match_id']
            : null;

        if (! $parentMatchId) {
            return null;
        }

        if ($eventType !== EsportMatch::EVENT_TYPE_HEAD_TO_HEAD) {
            throw new RuntimeException('Only head-to-head events can be linked to a parent tournament.');
        }

        $parentMatch = EsportMatch::query()->lockForUpdate()->findOrFail($parentMatchId);

        if (! $parentMatch->isTournamentRun()) {
            throw new RuntimeException('Parent event must be a tournament run.');
        }

        if (! $parentMatch->hasUnlockedChildMatches()) {
            throw new RuntimeException('Tournament child matches are still locked for this parent event.');
        }

        return $parentMatch;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{0: string, 1: string}
     */
    private function resolveLegacyTeams(string $eventType, array $payload, ?EsportMatch $parentMatch): array
    {
        if ($eventType === EsportMatch::EVENT_TYPE_TOURNAMENT_RUN) {
            return [
                'ERAH Rocket League',
                'Tournament Run',
            ];
        }

        $homeTeam = trim((string) ($payload['home_team'] ?? $payload['team_a_name'] ?? 'Equipe A'));
        $awayTeam = trim((string) ($payload['away_team'] ?? $payload['team_b_name'] ?? 'Equipe B'));

        return [$homeTeam, $awayTeam];
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function resolveEventName(string $eventType, array $payload, ?EsportMatch $parentMatch): ?string
    {
        if ($eventType === EsportMatch::EVENT_TYPE_TOURNAMENT_RUN) {
            $eventName = trim((string) ($payload['event_name'] ?? $payload['compétition_name'] ?? ''));

            return $eventName !== '' ? $eventName : 'Tournoi Rocket League';
        }

        return $parentMatch?->event_name;
    }
}
