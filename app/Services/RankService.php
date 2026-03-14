<?php

namespace App\Services;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Notifications\NotifyAction;
use App\Application\Actions\Ranking\EnsureUserProgressAction;
use App\Domain\Notifications\Enums\NotificationCategory;
use App\Models\League;
use App\Models\User;
use App\Models\UserProgress;
use App\Models\UserRankHistory;
use Illuminate\Support\Collection;

class RankService
{
    public function __construct(
        private readonly EnsureUserProgressAction $ensureUserProgressAction,
        private readonly NotifyAction $notifyAction,
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    /**
     * @return array{key: string, name: string, xp_threshold: int}
     */
    public function currentLeague(User $user): array
    {
        $progress = $this->ensureUserProgressAction->execute($user);

        return $this->resolveLeague((int) $progress->total_xp);
    }

    /**
     * @return array{key: string, name: string, xp_threshold: int}
     */
    public function resolveLeague(int $xp): array
    {
        $definitions = collect(config('community.xp_leagues', []))
            ->sortBy('xp_threshold')
            ->values();

        $league = $definitions
            ->filter(fn (array $definition): bool => $xp >= (int) ($definition['xp_threshold'] ?? 0))
            ->last();

        return $league ?: [
            'key' => 'bronze',
            'name' => 'Bronze',
            'xp_threshold' => 0,
        ];
    }

    public function sync(User $user): UserRankHistory
    {
        $progress = $this->ensureUserProgressAction->execute($user);
        $league = $this->resolveLeague((int) $progress->total_xp);
        $this->syncCurrentLeagueOnProgress($progress, $league);

        $latest = UserRankHistory::query()
            ->where('user_id', $user->id)
            ->latest('assigned_at')
            ->first();

        if ($latest && $latest->league_key === $league['key']) {
            return $latest;
        }

        $history = UserRankHistory::query()->create([
            'user_id' => $user->id,
            'league_key' => $league['key'],
            'league_name' => $league['name'],
            'xp_threshold' => (int) $league['xp_threshold'],
            'total_xp' => (int) $progress->total_xp,
            'meta' => [
                'previous_league_key' => $latest?->league_key,
                'previous_league_name' => $latest?->league_name,
            ],
            'assigned_at' => now(),
        ]);

        $this->storeAuditLogAction->execute(
            action: 'community.rank.synced',
            actor: $user,
            target: $history,
            context: [
                'league_key' => $league['key'],
                'league_name' => $league['name'],
                'total_xp' => $progress->total_xp,
            ],
        );

        if ($latest === null || $latest->league_key !== $history->league_key) {
            $this->notifyAction->execute(
                user: $user,
                category: NotificationCategory::SYSTEM->value,
                title: 'Nouveau rang communautaire',
                message: 'Vous passez au rang '.$history->league_name.'.',
                data: [
                    'league_key' => $history->league_key,
                    'league_name' => $history->league_name,
                    'total_xp' => $history->total_xp,
                ],
            );

            app(MissionEngine::class)->recordEvent($user, 'progress.rank.reached', 1, [
                'event_key' => 'progress.rank.reached.history.'.$history->id,
                'rank_key' => $history->league_key,
                'rank_name' => $history->league_name,
                'total_xp' => (int) $history->total_xp,
                'subject_type' => UserRankHistory::class,
                'subject_id' => (string) $history->id,
            ]);
        }

        return $history;
    }

    /**
     * @return Collection<int, array{key: string, name: string, xp_threshold: int}>
     */
    public function definitions(): Collection
    {
        return collect(config('community.xp_leagues', []))
            ->map(fn (array $definition): array => [
                'key' => (string) ($definition['key'] ?? ''),
                'name' => (string) ($definition['name'] ?? ''),
                'xp_threshold' => (int) ($definition['xp_threshold'] ?? 0),
            ])
            ->values();
    }

    /**
     * @param array{key: string, name: string, xp_threshold: int} $league
     */
    private function syncCurrentLeagueOnProgress(UserProgress $progress, array $league): void
    {
        if ($league['key'] === '') {
            return;
        }

        $databaseLeague = League::query()
            ->active()
            ->where('key', $league['key'])
            ->first();

        if (! $databaseLeague) {
            return;
        }

        if ((int) $progress->current_league_id !== (int) $databaseLeague->id) {
            $progress->current_league_id = $databaseLeague->id;
            $progress->save();
        }
    }
}
