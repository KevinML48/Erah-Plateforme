<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\InsufficientPointsException;
use App\Exceptions\OutOfStockException;
use App\Exceptions\RedemptionAlreadyProcessedException;
use App\Exceptions\RedemptionNotAllowedException;
use App\Exceptions\RewardNotAvailableException;
use App\Http\Requests\RedeemRewardRequest;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Services\EventTrackingService;
use App\Services\RedemptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RedemptionController extends Controller
{
    public function store(
        RedeemRewardRequest $request,
        Reward $reward,
        RedemptionService $redemptionService,
        EventTrackingService $eventTrackingService
    ): JsonResponse {
        $user = $request->user();
        abort_unless($user, 401);

        try {
            $redemption = $redemptionService->createRedemption(
                user: $user,
                reward: $reward,
                shippingData: $request->validated()
            );
        } catch (RewardNotAvailableException|OutOfStockException|InsufficientPointsException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        $eventTrackingService->trackAction($user, 'reward_redeemed', [
            'reward_id' => $reward->id,
            'redemption_id' => $redemption->id,
        ]);

        return response()->json([
            'message' => 'Redemption created.',
            'redemption' => $redemption,
        ], 201);
    }

    public function myIndex(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 401);

        $redemptions = RewardRedemption::query()
            ->with('reward:id,name,slug,image_url')
            ->where('user_id', $user->id)
            ->when($request->filled('status'), function ($query) use ($request): void {
                $query->where('status', (string) $request->string('status'));
            })
            ->orderByDesc('id')
            ->paginate(20);

        return response()->json($redemptions);
    }

    public function cancel(
        Request $request,
        RewardRedemption $redemption,
        RedemptionService $redemptionService
    ): JsonResponse {
        $user = $request->user();
        abort_unless($user, 401);

        try {
            $updated = $redemptionService->cancelRedemption($user, $redemption);
        } catch (RedemptionNotAllowedException|RedemptionAlreadyProcessedException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Redemption cancelled.',
            'redemption' => $updated,
        ]);
    }
}
