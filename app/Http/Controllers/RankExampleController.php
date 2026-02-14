<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RankExampleController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 401);

        $user->loadMissing('rank');

        return response()->json([
            'points_balance' => $user->points_balance,
            'rank' => $user->rank?->name,
            'next_rank' => $user->getNextRank()?->name,
            'progress_to_next_rank' => $user->getProgressToNextRank(),
        ]);
    }
}

