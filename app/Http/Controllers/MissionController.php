<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\MissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MissionController extends Controller
{
    public function index(Request $request, MissionService $missionService): JsonResponse|View
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $missions = $missionService->listActiveMissionsForUser($user, now(), 20);

        if ($request->expectsJson()) {
            return response()->json($missions);
        }

        return view('pages.missions.index', [
            'title' => 'Missions',
            'missions' => $missions,
        ]);
    }

    public function progression(Request $request, MissionService $missionService): JsonResponse|View
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $missions = $missionService->listInProgressMissionsForUser($user, now(), 20);

        if ($request->expectsJson()) {
            return response()->json($missions);
        }

        return view('pages.missions.current', [
            'title' => 'Vos missions',
            'missions' => $missions,
        ]);
    }

    public function show(Request $request, string $slug, MissionService $missionService): JsonResponse|View
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $mission = $missionService->getMissionBySlugForUser($slug, $user, now());

        if ($request->expectsJson()) {
            return response()->json([
                'mission' => $mission,
                'progress' => $mission->getAttribute('user_progress'),
            ]);
        }

        return view('pages.missions.show', [
            'title' => $mission->title,
            'mission' => $mission,
            'progress' => $mission->getAttribute('user_progress'),
        ]);
    }

    public function history(Request $request, MissionService $missionService): JsonResponse|View
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $history = $missionService->listCompletedMissionsForUser($user, 20);

        if ($request->expectsJson()) {
            return response()->json($history);
        }

        return view('pages.missions.history', [
            'title' => 'Missions finies',
            'history' => $history,
        ]);
    }
}
