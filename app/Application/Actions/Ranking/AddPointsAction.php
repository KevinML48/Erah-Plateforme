<?php

namespace App\Application\Actions\Ranking;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Notifications\NotifyAction;
use App\Domain\Ranking\DataTransferObjects\AddPointsResult;
use App\Models\League;
use App\Models\LeaguePromotion;
use App\Models\PointsTransaction;
use App\Models\User;
use App\Models\UserProgress;
use App\Services\RankService;
use App\Services\StreakService;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AddPointsAction
{
    public function __construct(
        private readonly EnsureUserProgressAction $ensureUserProgressAction,
        private readonly StoreAuditLogAction $storeAuditLogAction,
        private readonly NotifyAction $notifyAction,
        private readonly StreakService $streakService,
        private readonly RankService $rankService
    ) {
    }

    public function execute(
        User $user,
        string $kind,
        int $points,
        string $sourceType,
        string $sourceId,
        ?User $actor = null,
        array $meta = []
    ): AddPointsResult {
        if (! in_array($kind, [PointsTransaction::KIND_XP, PointsTransaction::KIND_RANK], true)) {
            throw new RuntimeException('Unsupported points kind.');
        }

        if ($points <= 0) {
            throw new RuntimeException('Points must be positive.');
        }

        if ($kind === PointsTransaction::KIND_XP && $user->isSupporterActive()) {
            $points = max(1, (int) ceil($points * (float) config('supporter.xp_multiplier', 1)));
        }

        if ($kind === PointsTransaction::KIND_XP) {
            $points = max(1, (int) ceil($points * $this->streakService->xpMultiplierFor($user)));
        }

        try {
            $result = DB::transaction(function () use ($user, $kind, $points, $sourceType, $sourceId, $actor, $meta) {
                $progress = $this->ensureUserProgressAction->execute($user);
                $progress = UserProgress::query()
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $existing = PointsTransaction::query()
                    ->where('user_id', $user->id)
                    ->where('kind', $kind)
                    ->where('source_type', $sourceType)
                    ->where('source_id', $sourceId)
                    ->lockForUpdate()
                    ->first();

                if ($existing) {
                    $this->storeAuditLogAction->execute(
                        action: 'ranking.points.idempotent_hit',
                        actor: $actor,
                        target: $existing,
                        context: [
                            'user_id' => $user->id,
                            'kind' => $kind,
                            'source_type' => $sourceType,
                            'source_id' => $sourceId,
                        ],
                    );

                    $promotions = LeaguePromotion::query()
                        ->where('points_transaction_id', $existing->id)
                        ->orderBy('id')
                        ->get();

                    return new AddPointsResult(
                        idempotent: true,
                        transaction: $existing,
                        progress: $progress->fresh(['league']),
                        promotions: $promotions,
                    );
                }

                $beforeXp = $progress->total_xp;
                $beforeRankPoints = $progress->total_rank_points;

                if ($kind === PointsTransaction::KIND_XP) {
                    $progress->total_xp += $points;
                }

                if ($kind === PointsTransaction::KIND_RANK) {
                    $progress->total_rank_points += $points;
                }

                $progress->last_points_at = now();
                $progress->save();

                $transaction = PointsTransaction::query()->create([
                    'user_id' => $user->id,
                    'kind' => $kind,
                    'points' => $points,
                    'source_type' => $sourceType,
                    'source_id' => $sourceId,
                    'meta' => $meta,
                    'before_xp' => $beforeXp,
                    'after_xp' => $progress->total_xp,
                    'before_rank_points' => $beforeRankPoints,
                    'after_rank_points' => $progress->total_rank_points,
                ]);

                $promotions = collect();
                if ($kind === PointsTransaction::KIND_RANK) {
                    $promotions = $this->applyPromotions($user, $progress, $transaction, $actor);
                }

                $this->storeAuditLogAction->execute(
                    action: 'ranking.points.granted',
                    actor: $actor,
                    target: $transaction,
                    context: [
                        'user_id' => $user->id,
                        'kind' => $kind,
                        'points' => $points,
                        'source_type' => $sourceType,
                        'source_id' => $sourceId,
                    ],
                );

                return new AddPointsResult(
                    idempotent: false,
                    transaction: $transaction,
                    progress: $progress->fresh(['league']),
                    promotions: $promotions,
                );
            });

            if ($kind === PointsTransaction::KIND_XP) {
                $this->rankService->sync($user);
            }

            return $result;
        } catch (QueryException $exception) {
            $message = $exception->getMessage();
            $isIdempotenceCollision = str_contains($message, 'points_idempotence_unique')
                || str_contains($message, 'UNIQUE constraint failed: points_transactions.user_id, points_transactions.kind, points_transactions.source_type, points_transactions.source_id');

            if (! $isIdempotenceCollision) {
                throw $exception;
            }

            $existing = PointsTransaction::query()
                ->where('user_id', $user->id)
                ->where('kind', $kind)
                ->where('source_type', $sourceType)
                ->where('source_id', $sourceId)
                ->firstOrFail();

            $progress = $this->ensureUserProgressAction->execute($user);
            $promotions = LeaguePromotion::query()
                ->where('points_transaction_id', $existing->id)
                ->orderBy('id')
                ->get();

            $result = new AddPointsResult(
                idempotent: true,
                transaction: $existing,
                progress: $progress->fresh(['league']),
                promotions: $promotions,
            );

            if ($kind === PointsTransaction::KIND_XP) {
                $this->rankService->sync($user);
            }

            return $result;
        }
    }

    /**
     * @return Collection<int, LeaguePromotion>
     */
    private function applyPromotions(
        User $user,
        UserProgress $progress,
        PointsTransaction $transaction,
        ?User $actor = null
    ): Collection {
        $currentLeague = $progress->league ?: $this->resolveLeagueForPoints($progress->total_rank_points);
        $targetLeague = $this->resolveLeagueForPoints($progress->total_rank_points);

        if (! $targetLeague) {
            throw new RuntimeException('No active league configured.');
        }

        if (! $currentLeague) {
            $progress->current_league_id = $targetLeague->id;
            $progress->save();

            return collect();
        }

        if ($targetLeague->sort_order <= $currentLeague->sort_order) {
            return collect();
        }

        /** @var EloquentCollection<int, League> $promotedLeagues */
        $promotedLeagues = League::query()
            ->active()
            ->whereBetween('sort_order', [$currentLeague->sort_order + 1, $targetLeague->sort_order])
            ->orderBy('sort_order')
            ->get();

        $promotions = collect();
        $fromLeague = $currentLeague;

        foreach ($promotedLeagues as $league) {
            $promotion = LeaguePromotion::query()->create([
                'user_id' => $user->id,
                'from_league_id' => $fromLeague?->id,
                'to_league_id' => $league->id,
                'points_transaction_id' => $transaction->id,
                'rank_points' => $progress->total_rank_points,
                'promoted_at' => now(),
            ]);

            $this->storeAuditLogAction->execute(
                action: 'ranking.league.promoted',
                actor: $actor,
                target: $promotion,
                context: [
                    'user_id' => $user->id,
                    'from_league' => $fromLeague?->key,
                    'to_league' => $league->key,
                    'rank_points' => $progress->total_rank_points,
                    'points_transaction_id' => $transaction->id,
                ],
            );

            $this->notifyAction->execute(
                user: $user,
                category: 'system',
                message: 'Vous accedez a la ligue '.$league->name.'.',
                title: 'Nouvelle ligue debloquee',
                data: [
                    'from' => $fromLeague?->name,
                    'to' => $league->name,
                    'to_league_key' => $league->key,
                    'rank_points' => $progress->total_rank_points,
                ],
            );

            $progress->current_league_id = $league->id;
            $progress->save();

            $promotions->push($promotion);
            $fromLeague = $league;
        }

        return $promotions;
    }

    private function resolveLeagueForPoints(int $rankPoints): ?League
    {
        return League::query()
            ->active()
            ->where('min_rank_points', '<=', $rankPoints)
            ->orderByDesc('min_rank_points')
            ->first();
    }
}
