<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\MissionCatalogService;
use App\Services\MissionEngine;
use App\Services\MissionFocusService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MissionPageController extends Controller
{
    public function index(
        Request $request,
        MissionCatalogService $missionCatalogService,
        MissionFocusService $missionFocusService,
        MissionEngine $missionEngine
    ): View {
        $user = auth()->user();
        $today = now()->toDateString();

        $missionEngine->recordEvent($user, 'mission.board.view', 1, [
            'event_key' => 'mission.board.view.'.$user->id.'.'.$today,
            'date' => $today,
            'subject_type' => User::class,
            'subject_id' => (string) $user->id,
        ]);

        $payload = $missionCatalogService->dashboardPayload(auth()->user(), [
            'type' => $request->query('type'),
            'difficulty' => $request->query('difficulty'),
            'status' => $request->query('status'),
            'duration' => $request->query('duration'),
        ]);
        $isPublicApp = request()->routeIs('app.*');

        return view('pages.missions.index', [
            'missionSummary' => $payload['summary'],
            'discoveryCards' => collect($payload['discovery']),
            'focusCards' => collect($payload['focus']),
            'allCards' => collect($payload['active']),
            'history' => $payload['history'],
            'missionFilters' => $payload['filters'],
            'missionFilterOptions' => $payload['filter_options'],
            'focusLimit' => MissionFocusService::MAX_FOCUS_MISSIONS,
            'focusTemplateIds' => $missionFocusService->forUser(auth()->user())
                ->pluck('mission_template_id')
                ->map(fn (mixed $id): int => (int) $id)
                ->all(),
            'missionFocusStoreRoute' => $isPublicApp ? 'app.missions.focus.store' : 'missions.focus.store',
            'missionFocusDestroyRoute' => $isPublicApp ? 'app.missions.focus.destroy' : 'missions.focus.destroy',
            'missionClaimRoute' => $isPublicApp ? 'app.missions.claim' : 'missions.claim',
            'dashboardRouteName' => $isPublicApp ? 'app.leaderboards.me' : 'dashboard',
        ]);
    }
}
