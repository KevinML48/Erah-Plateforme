<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\PointTransactionType;
use App\Exceptions\InsufficientPointsException;
use App\Services\PointService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PointsExampleController extends Controller
{
    public function awardWin(Request $request, PointService $pointService): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user, 401);

        $pointService->addPoints(
            user: $user,
            amount: 100,
            type: PointTransactionType::BetWin->value,
            description: 'Bet victory reward',
            referenceId: 1,
            referenceType: 'bet'
        );

        return back()->with('status', '100 points added.');
    }

    public function purchaseReward(Request $request, PointService $pointService): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user, 401);

        try {
            $pointService->removePoints(
                user: $user,
                amount: 500,
                type: PointTransactionType::RewardPurchase->value,
                description: 'Reward purchase',
                referenceId: 1,
                referenceType: 'reward'
            );
        } catch (InsufficientPointsException $exception) {
            return back()->withErrors([
                'points' => $exception->getMessage(),
            ]);
        }

        return back()->with('status', '500 points removed.');
    }
}

