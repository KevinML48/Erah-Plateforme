<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\RewardCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class RewardController extends Controller
{
    public function index(Request $request, RewardCatalogService $catalogService): JsonResponse|View
    {
        $rewards = $catalogService->listActiveRewards($request->user(), (int) $request->integer('per_page', 20));

        if (!$request->expectsJson()) {
            return view('pages.rewards.index', [
                'title' => 'Rewards',
                'rewards' => $rewards,
            ]);
        }

        return response()->json($rewards);
    }

    public function show(string $slug, Request $request, RewardCatalogService $catalogService): JsonResponse|View
    {
        $reward = $catalogService->getRewardBySlug($slug);

        if (!$request->expectsJson()) {
            return view('pages.rewards.show', [
                'title' => 'Reward Details',
                'reward' => $reward,
            ]);
        }

        return response()->json(['reward' => $reward]);
    }
}
