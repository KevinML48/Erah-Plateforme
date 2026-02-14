<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\LeaderboardStat;
use App\Models\PointLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;

class LeaderboardService
{
    private const CACHE_VERSION_KEY_PREFIX = 'leaderboard:version:';

    public function getAllTimeLeaderboard(int $limit = 50, ?string $search = null): EloquentCollection
    {
        $limit = $this->sanitizeLimit($limit);
        $search = $this->sanitizeSearch($search);
        $cacheKey = $this->leaderboardCacheKey('all_time', $limit, $search);

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($limit, $search): EloquentCollection {
            return User::query()
                ->select(['id', 'name', 'avatar_url', 'points_balance', 'rank_id'])
                ->with('rank:id,name,slug,badge_color')
                ->when($search !== null, fn ($query) => $query->where('name', 'like', '%'.$search.'%'))
                ->orderByDesc('points_balance')
                ->orderBy('id')
                ->limit($limit)
                ->get();
        });
    }

    public function getWeeklyLeaderboard(int $limit = 50, ?string $search = null): Collection
    {
        return $this->getPeriodLeaderboard(days: 7, cacheType: 'weekly', cacheMinutes: 2, limit: $limit, search: $search);
    }

    public function getMonthlyLeaderboard(int $limit = 50, ?string $search = null): Collection
    {
        return $this->getPeriodLeaderboard(days: 30, cacheType: 'monthly', cacheMinutes: 2, limit: $limit, search: $search);
    }

    public function getUserRankPosition(User $user, string $type = 'all_time'): int
    {
        return match ($type) {
            'all_time' => $this->getAllTimeUserPosition($user),
            'weekly' => $this->getPeriodUserPosition($user, 7),
            'monthly' => $this->getPeriodUserPosition($user, 30),
            default => throw new InvalidArgumentException('Unsupported leaderboard type.'),
        };
    }

    private function getPeriodLeaderboard(int $days, string $cacheType, int $cacheMinutes, int $limit, ?string $search = null): Collection
    {
        $limit = $this->sanitizeLimit($limit);
        $search = $this->sanitizeSearch($search);
        $cacheKey = $this->leaderboardCacheKey($cacheType, $limit, $search);

        return Cache::remember($cacheKey, now()->addMinutes($cacheMinutes), function () use ($days, $limit, $search): Collection {
            if (!Schema::hasTable('leaderboard_stats')) {
                return $this->getPeriodLeaderboardFromLogs($days, $limit, $search);
            }

            $period = $days === 7
                ? LeaderboardStatsService::PERIOD_WEEKLY
                : LeaderboardStatsService::PERIOD_MONTHLY;

            return DB::query()
                ->from('users as u')
                ->leftJoin('leaderboard_stats as ls', function ($join) use ($period): void {
                    $join
                        ->on('u.id', '=', 'ls.user_id')
                        ->where('ls.period', '=', $period);
                })
                ->leftJoin('ranks as r', 'r.id', '=', 'u.rank_id')
                ->when($search !== null, fn ($query) => $query->where('u.name', 'like', '%'.$search.'%'))
                ->orderByDesc(DB::raw('COALESCE(ls.points_total, 0)'))
                ->orderBy('u.id')
                ->limit($limit)
                ->get([
                    'u.id',
                    'u.name',
                    'u.avatar_url',
                    'u.points_balance',
                    DB::raw('COALESCE(ls.points_total, 0) as period_points'),
                    'r.id as rank_id',
                    'r.name as rank_name',
                    'r.slug as rank_slug',
                    'r.badge_color as rank_badge_color',
                ]);
        });
    }

    private function getAllTimeUserPosition(User $user): int
    {
        $higherUsers = User::query()
            ->where(function ($query) use ($user): void {
                $query
                    ->where('points_balance', '>', (int) $user->points_balance)
                    ->orWhere(function ($tieQuery) use ($user): void {
                        $tieQuery
                            ->where('points_balance', '=', (int) $user->points_balance)
                            ->where('id', '<', (int) $user->id);
                    });
            })
            ->count();

        return $higherUsers + 1;
    }

    private function getPeriodUserPosition(User $user, int $days): int
    {
        if (!Schema::hasTable('leaderboard_stats')) {
            return $this->getPeriodUserPositionFromLogs($user, $days);
        }

        $period = $days === 7
            ? LeaderboardStatsService::PERIOD_WEEKLY
            : LeaderboardStatsService::PERIOD_MONTHLY;

        $userPeriodPoints = (int) LeaderboardStat::query()
            ->where('user_id', $user->id)
            ->where('period', $period)
            ->value('points_total');

        $periodSubQuery = LeaderboardStat::query()
            ->select(['user_id', 'points_total as period_points'])
            ->where('period', $period);

        $higherUsers = DB::query()
            ->fromSub($periodSubQuery, 'period')
            ->where(function ($query) use ($user, $userPeriodPoints): void {
                $query
                    ->where('period.period_points', '>', $userPeriodPoints)
                    ->orWhere(function ($tieQuery) use ($user, $userPeriodPoints): void {
                        $tieQuery
                            ->where('period.period_points', '=', $userPeriodPoints)
                            ->where('period.user_id', '<', (int) $user->id);
                    });
            })
            ->count();

        return $higherUsers + 1;
    }

    public function invalidateCache(): void
    {
        foreach (['all_time', 'weekly', 'monthly'] as $type) {
            Cache::increment(self::CACHE_VERSION_KEY_PREFIX.$type);
            Cache::forget("leaderboard:{$type}");
        }
    }

    private function leaderboardCacheKey(string $type, int $limit, ?string $search = null): string
    {
        $version = (int) Cache::get(self::CACHE_VERSION_KEY_PREFIX.$type, 1);
        $searchToken = $search !== null ? ':search:'.md5($search) : '';

        return $limit === 50
            ? "leaderboard:{$type}:v{$version}{$searchToken}"
            : "leaderboard:{$type}:limit:{$limit}:v{$version}{$searchToken}";
    }

    private function sanitizeLimit(int $limit): int
    {
        return min(200, max(1, $limit));
    }

    private function sanitizeSearch(?string $search): ?string
    {
        $value = trim((string) $search);

        return $value === '' ? null : mb_substr($value, 0, 100);
    }

    private function getPeriodLeaderboardFromLogs(int $days, int $limit, ?string $search = null): Collection
    {
        $since = now()->subDays($days);

        $periodSubQuery = PointLog::query()
            ->selectRaw('user_id, SUM(amount) as period_points')
            ->where('amount', '>', 0)
            ->where('created_at', '>=', $since)
            ->groupBy('user_id');

        return DB::query()
            ->fromSub($periodSubQuery, 'period')
            ->join('users as u', 'u.id', '=', 'period.user_id')
            ->leftJoin('ranks as r', 'r.id', '=', 'u.rank_id')
            ->when($search !== null, fn ($query) => $query->where('u.name', 'like', '%'.$search.'%'))
            ->orderByDesc('period.period_points')
            ->orderBy('u.id')
            ->limit($limit)
            ->get([
                'u.id',
                'u.name',
                'u.avatar_url',
                'u.points_balance',
                DB::raw('period.period_points as period_points'),
                'r.id as rank_id',
                'r.name as rank_name',
                'r.slug as rank_slug',
                'r.badge_color as rank_badge_color',
            ]);
    }

    private function getPeriodUserPositionFromLogs(User $user, int $days): int
    {
        $since = now()->subDays($days);

        $userPeriodPoints = (int) PointLog::query()
            ->where('user_id', $user->id)
            ->where('amount', '>', 0)
            ->where('created_at', '>=', $since)
            ->sum('amount');

        $periodSubQuery = PointLog::query()
            ->selectRaw('user_id, SUM(amount) as period_points')
            ->where('amount', '>', 0)
            ->where('created_at', '>=', $since)
            ->groupBy('user_id');

        $higherUsers = DB::query()
            ->fromSub($periodSubQuery, 'period')
            ->where(function ($query) use ($user, $userPeriodPoints): void {
                $query
                    ->where('period.period_points', '>', $userPeriodPoints)
                    ->orWhere(function ($tieQuery) use ($user, $userPeriodPoints): void {
                        $tieQuery
                            ->where('period.period_points', '=', $userPeriodPoints)
                            ->where('period.user_id', '<', (int) $user->id);
                    });
            })
            ->count();

        return $higherUsers + 1;
    }
}
