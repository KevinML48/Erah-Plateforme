<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\UserMission;
use App\Services\MissionClaimService;
use Illuminate\Http\RedirectResponse;
use RuntimeException;

class MissionClaimController extends Controller
{
    public function store(UserMission $mission, MissionClaimService $missionClaimService): RedirectResponse
    {
        try {
            $missionClaimService->claim(auth()->user(), $mission);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Recompense mission reclamee.');
    }
}
