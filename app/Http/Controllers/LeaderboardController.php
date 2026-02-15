<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\EventTrackingService;
use App\Services\LeaderboardService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function __construct(
        private readonly LeaderboardService $leaderboardService,
        private readonly EventTrackingService $eventTrackingService
    ) {
    }

    public function allTime(Request $request): JsonResponse|View
    {
        $limit = (int) $request->integer('limit', 50);
        $search = $request->string('q')->toString();
        $leaderboard = $this->normalizeEntries(
            type: 'all_time',
            rawEntries: $this->leaderboardService->getAllTimeLeaderboard($limit, $search),
        );
        $user = $request->user();
        if ($user) {
            $this->eventTrackingService->trackAction($user, 'leaderboard_viewed', ['type' => 'all_time']);
        }
        $position = $user ? $this->leaderboardService->getUserRankPosition($user, 'all_time') : null;

        if ($request->expectsJson()) {
            return response()->json([
                'type' => 'all_time',
                'limit' => $limit,
                'search' => $search,
                'data' => $leaderboard,
                'current_user_position' => $position,
            ]);
        }

        return view('pages.leaderboard.index', [
            'title' => 'Leaderboard All-time',
            'type' => 'all_time',
            'limit' => $limit,
            'search' => $search,
            'entries' => $leaderboard,
            'currentUserPosition' => $position,
        ]);
    }

    public function weekly(Request $request): JsonResponse|View
    {
        $limit = (int) $request->integer('limit', 50);
        $search = $request->string('q')->toString();
        $leaderboard = $this->normalizeEntries(
            type: 'weekly',
            rawEntries: $this->leaderboardService->getWeeklyLeaderboard($limit, $search),
        );
        $user = $request->user();
        if ($user) {
            $this->eventTrackingService->trackAction($user, 'leaderboard_viewed', ['type' => 'weekly']);
        }
        $position = $user ? $this->leaderboardService->getUserRankPosition($user, 'weekly') : null;

        if ($request->expectsJson()) {
            return response()->json([
                'type' => 'weekly',
                'limit' => $limit,
                'search' => $search,
                'data' => $leaderboard,
                'current_user_position' => $position,
            ]);
        }

        return view('pages.leaderboard.index', [
            'title' => 'Leaderboard Weekly',
            'type' => 'weekly',
            'limit' => $limit,
            'search' => $search,
            'entries' => $leaderboard,
            'currentUserPosition' => $position,
        ]);
    }

    public function monthly(Request $request): JsonResponse|View
    {
        $limit = (int) $request->integer('limit', 50);
        $search = $request->string('q')->toString();
        $leaderboard = $this->normalizeEntries(
            type: 'monthly',
            rawEntries: $this->leaderboardService->getMonthlyLeaderboard($limit, $search),
        );
        $user = $request->user();
        if ($user) {
            $this->eventTrackingService->trackAction($user, 'leaderboard_viewed', ['type' => 'monthly']);
        }
        $position = $user ? $this->leaderboardService->getUserRankPosition($user, 'monthly') : null;

        if ($request->expectsJson()) {
            return response()->json([
                'type' => 'monthly',
                'limit' => $limit,
                'search' => $search,
                'data' => $leaderboard,
                'current_user_position' => $position,
            ]);
        }

        return view('pages.leaderboard.index', [
            'title' => 'Leaderboard Monthly',
            'type' => 'monthly',
            'limit' => $limit,
            'search' => $search,
            'entries' => $leaderboard,
            'currentUserPosition' => $position,
        ]);
    }

    private function normalizeEntries(string $type, mixed $rawEntries): Collection
    {
        $entries = collect($rawEntries);

        return $entries->values()->map(function ($entry, int $index) use ($type): array {
            if ($type === 'all_time') {
                return [
                    'position' => $index + 1,
                    'user_id' => (int) $entry->id,
                    'name' => (string) $entry->name,
                    'avatar_url' => $entry->avatar_url,
                    'rank_name' => $entry->rank?->name,
                    'score' => (int) $entry->points_balance,
                ];
            }

            return [
                'position' => $index + 1,
                'user_id' => (int) $entry->id,
                'name' => (string) $entry->name,
                'avatar_url' => $entry->avatar_url,
                'rank_name' => $entry->rank_name,
                'score' => (int) $entry->period_points,
            ];
        });
    }
}
