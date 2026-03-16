<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MissionTemplate;
use App\Models\User;
use App\Services\MissionFocusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class MissionFocusController extends Controller
{
    public function store(MissionTemplate $template, MissionFocusService $missionFocusService): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $missionFocusService->add($user, $template);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Mission ajoutee aux favoris.');
    }

    public function destroy(MissionTemplate $template, MissionFocusService $missionFocusService): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $missionFocusService->remove($user, $template);

        return back()->with('success', 'Mission retiree des favoris.');
    }
}
