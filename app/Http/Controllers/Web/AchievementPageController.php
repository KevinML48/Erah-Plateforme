<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AchievementService;
use Illuminate\View\View;

class AchievementPageController extends Controller
{
    public function __invoke(AchievementService $achievementService): View
    {
        $user = auth()->user();
        $achievementService->sync($user);

        $achievements = $user->userAchievements()
            ->with('achievement')
            ->latest('unlocked_at')
            ->paginate(18)
            ->withQueryString();

        return view('pages.achievements.index', [
            'achievements' => $achievements,
        ]);
    }
}
