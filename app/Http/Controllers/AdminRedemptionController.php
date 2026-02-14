<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\RedemptionAlreadyProcessedException;
use App\Exceptions\RedemptionNotAllowedException;
use App\Http\Requests\RejectRedemptionRequest;
use App\Http\Requests\ShipRedemptionRequest;
use App\Models\RewardRedemption;
use App\Services\RedemptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminRedemptionController extends Controller
{
    public function index(Request $request): JsonResponse|View
    {
        $redemptions = RewardRedemption::query()
            ->with(['user:id,name,email', 'reward:id,name,slug,points_cost'])
            ->when($request->filled('status'), function ($query) use ($request): void {
                $query->where('status', (string) $request->string('status'));
            })
            ->orderByDesc('id')
            ->paginate(50);

        if (!$request->expectsJson()) {
            return view('pages.admin.redemptions.index', [
                'title' => 'Admin Redemptions',
                'redemptions' => $redemptions,
            ]);
        }

        return response()->json($redemptions);
    }

    public function approve(
        Request $request,
        RewardRedemption $redemption,
        RedemptionService $redemptionService
    ): JsonResponse|RedirectResponse {
        try {
            $updated = $redemptionService->approveRedemption($request->user(), $redemption);
        } catch (RedemptionNotAllowedException|RedemptionAlreadyProcessedException $exception) {
            if (!$request->expectsJson()) {
                return back()->withErrors(['redemption' => $exception->getMessage()]);
            }
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        if (!$request->expectsJson()) {
            return redirect()->route('admin.redemptions.index')->with('status', 'Redemption approved.');
        }

        return response()->json([
            'message' => 'Redemption approved.',
            'redemption' => $updated,
        ]);
    }

    public function reject(
        RejectRedemptionRequest $request,
        RewardRedemption $redemption,
        RedemptionService $redemptionService
    ): JsonResponse|RedirectResponse {
        try {
            $updated = $redemptionService->rejectRedemption(
                admin: $request->user(),
                redemption: $redemption,
                note: $request->input('note')
            );
        } catch (RedemptionNotAllowedException|RedemptionAlreadyProcessedException $exception) {
            if (!$request->expectsJson()) {
                return back()->withErrors(['redemption' => $exception->getMessage()]);
            }
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        if (!$request->expectsJson()) {
            return redirect()->route('admin.redemptions.index')->with('status', 'Redemption rejected.');
        }

        return response()->json([
            'message' => 'Redemption rejected.',
            'redemption' => $updated,
        ]);
    }

    public function ship(
        ShipRedemptionRequest $request,
        RewardRedemption $redemption,
        RedemptionService $redemptionService
    ): JsonResponse|RedirectResponse {
        try {
            $updated = $redemptionService->markShipped(
                admin: $request->user(),
                redemption: $redemption,
                tracking: $request->input('tracking_code')
            );
        } catch (RedemptionNotAllowedException $exception) {
            if (!$request->expectsJson()) {
                return back()->withErrors(['redemption' => $exception->getMessage()]);
            }
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        if (!$request->expectsJson()) {
            return redirect()->route('admin.redemptions.index')->with('status', 'Redemption shipped.');
        }

        return response()->json([
            'message' => 'Redemption shipped.',
            'redemption' => $updated,
        ]);
    }
}
