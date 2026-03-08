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
        private readonly StoreAuditLogAction $storeAuditLogAction,
        private readonly SyncMatchMarketsAction $syncMatchMarketsAction
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

            $eventType = (string) ($payload['event_type'] ?? $lockedMatch->event_type ?? EsportMatch::EVENT_TYPE_HEAD_TO_HEAD);
            $startsAt = \Illuminate\Support\Carbon::parse((string) $payload['starts_at']);
            $lockedAt = ! empty($payload['locked_at'])
                ? \Illuminate\Support\Carbon::parse((string) $payload['locked_at'])
                : $startsAt->copy()->subMinutes((int) config('betting.match.default_lock_offset_minutes', 5));
            $endsAt = ! empty($payload['ends_at'])
                ? \Illuminate\Support\Carbon::parse((string) $payload['ends_at'])
                : null;
            $parentMatch = $this->resolveParentMatch($lockedMatch, $eventType, $payload);
            [$homeTeam, $awayTeam] = $this->resolveLegacyTeams($eventType, $payload, $lockedMatch);
            $teamAName = $eventType === EsportMatch::EVENT_TYPE_TOURNAMENT_RUN ? null : $homeTeam;
            $teamBName = $eventType === EsportMatch::EVENT_TYPE_TOURNAMENT_RUN ? null : $awayTeam;

            $lockedMatch->fill([
                'game_key' => $payload['game_key'] ?? $parentMatch?->game_key ?? $lockedMatch->game_key,
                'event_type' => $eventType,
                'event_name' => $this->resolveEventName($eventType, $payload, $parentMatch, $lockedMatch),
                'competition_name' => $payload['competition_name'] ?? $parentMatch?->competition_name,
                'competition_stage' => $payload['competition_stage'] ?? $parentMatch?->competition_stage,
                'competition_split' => $payload['competition_split'] ?? $parentMatch?->competition_split,
                'best_of' => isset($payload['best_of']) && $payload['best_of'] !== '' ? (int) $payload['best_of'] : null,
                'parent_match_id' => $parentMatch?->id,
                'team_a_name' => $teamAName,
                'team_b_name' => $teamBName,
                'home_team' => $homeTeam,
                'away_team' => $awayTeam,
                'starts_at' => $startsAt,
                'locked_at' => $lockedAt,
                'ends_at' => $endsAt,
                'updated_by' => $actor->id,
            ]);
            $lockedMatch->save();

            $this->syncMatchMarketsAction->execute($lockedMatch, $payload['markets'] ?? null, array_merge($payload, [
                'team_a_name' => $teamAName,
                'team_b_name' => $teamBName,
                'home_team' => $homeTeam,
                'away_team' => $awayTeam,
            ]));

            $this->storeAuditLogAction->execute(
                action: 'matches.updated',
                actor: $actor,
                target: $lockedMatch,
                context: [
                    'match_id' => $lockedMatch->id,
                    'match_key' => $lockedMatch->match_key,
                    'event_type' => $lockedMatch->event_type,
                    'game_key' => $lockedMatch->game_key,
                    'status' => $lockedMatch->status,
                ],
            );

            return $lockedMatch->fresh();
        });
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function resolveParentMatch(EsportMatch $match, string $eventType, array $payload): ?EsportMatch
    {
        $parentMatchId = isset($payload['parent_match_id']) && $payload['parent_match_id'] !== ''
            ? (int) $payload['parent_match_id']
            : null;

        if (! $parentMatchId) {
            return null;
        }

        if ($parentMatchId === $match->id) {
            throw new RuntimeException('A match cannot be its own parent.');
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
    private function resolveLegacyTeams(string $eventType, array $payload, EsportMatch $match): array
    {
        if ($eventType === EsportMatch::EVENT_TYPE_TOURNAMENT_RUN) {
            return [
                'ERAH Rocket League',
                'Tournament Run',
            ];
        }

        $homeTeam = trim((string) ($payload['home_team'] ?? $payload['team_a_name'] ?? $match->team_a_name ?? $match->home_team ?? 'Equipe A'));
        $awayTeam = trim((string) ($payload['away_team'] ?? $payload['team_b_name'] ?? $match->team_b_name ?? $match->away_team ?? 'Equipe B'));

        return [$homeTeam, $awayTeam];
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function resolveEventName(
        string $eventType,
        array $payload,
        ?EsportMatch $parentMatch,
        EsportMatch $match
    ): ?string {
        if ($eventType === EsportMatch::EVENT_TYPE_TOURNAMENT_RUN) {
            $eventName = trim((string) ($payload['event_name'] ?? $payload['competition_name'] ?? ''));

            return $eventName !== '' ? $eventName : ($match->event_name ?: 'Tournoi Rocket League');
        }

        return $parentMatch?->event_name;
    }
}
