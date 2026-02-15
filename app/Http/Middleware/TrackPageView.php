<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\EventTrackingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    public function __construct(
        private readonly EventTrackingService $eventTrackingService
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $user = $request->user();

        if (!$user || !$request->isMethod('GET')) {
            return $response;
        }

        $routeName = (string) optional($request->route())->getName();
        if ($routeName === '') {
            return $response;
        }

        $pageKey = $this->resolvePageKey($routeName);

        $this->eventTrackingService->trackPageView($user, $pageKey);

        return $response;
    }

    private function resolvePageKey(string $routeName): string
    {
        return match (true) {
            str_starts_with($routeName, 'dashboard') => 'dashboard',
            str_starts_with($routeName, 'matches.') => 'matches',
            str_starts_with($routeName, 'rewards.') => 'rewards',
            str_starts_with($routeName, 'leaderboard.') => 'leaderboard',
            str_starts_with($routeName, 'missions.') => 'missions',
            str_starts_with($routeName, 'profile') => 'profile',
            default => str_replace('.', '_', $routeName),
        };
    }
}
