<?php

namespace App\Http\Controllers\Web;

use App\Application\Actions\Rewards\EnsureCurrentMissionInstancesAction;
use App\Http\Controllers\Controller;
use App\Models\MissionTemplate;
use App\Models\UserMission;
use Illuminate\View\View;

class MissionPageController extends Controller
{
    public function index(EnsureCurrentMissionInstancesAction $ensureCurrentMissionInstancesAction): View
    {
        $user = auth()->user();
        $ensureCurrentMissionInstancesAction->execute($user);

        $todayStart = now()->startOfDay();
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        $dailyMissions = UserMission::query()
            ->where('user_id', $user->id)
            ->whereHas('instance.template', function ($query): void {
                $query->where('scope', MissionTemplate::SCOPE_DAILY);
            })
            ->whereHas('instance', function ($query) use ($todayStart): void {
                $query->whereDate('period_start', $todayStart->toDateString());
            })
            ->with(['instance.template', 'completion'])
            ->orderByDesc('completed_at')
            ->orderByDesc('updated_at')
            ->get();

        $weeklyMissions = UserMission::query()
            ->where('user_id', $user->id)
            ->whereHas('instance.template', function ($query): void {
                $query->where('scope', MissionTemplate::SCOPE_WEEKLY);
            })
            ->whereHas('instance', function ($query) use ($weekStart, $weekEnd): void {
                $query->whereBetween('period_start', [$weekStart, $weekEnd]);
            })
            ->with(['instance.template', 'completion'])
            ->orderByDesc('completed_at')
            ->orderByDesc('updated_at')
            ->get();

        $history = UserMission::query()
            ->where('user_id', $user->id)
            ->with(['instance.template', 'completion'])
            ->latest('updated_at')
            ->paginate(20);

        return view('pages.missions.index', [
            'dailyMissions' => $dailyMissions,
            'weeklyMissions' => $weeklyMissions,
            'history' => $history,
        ]);
    }
}

