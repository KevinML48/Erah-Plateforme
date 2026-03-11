<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MissionTemplate;
use App\Services\MissionFocusService;
use Illuminate\Http\RedirectResponse;
use RuntimeException;

class MissionFocusController extends Controller
{
    public function store(MissionTemplate $template, MissionFocusService $missionFocusService): RedirectResponse
    {
        try {
            $missionFocusService->add(auth()->user(), $template);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Mission ajoutee en focus.');
    }

    public function destroy(MissionTemplate $template, MissionFocusService $missionFocusService): RedirectResponse
    {
        $missionFocusService->remove(auth()->user(), $template);

        return back()->with('success', 'Mission retiree du focus.');
    }
}
