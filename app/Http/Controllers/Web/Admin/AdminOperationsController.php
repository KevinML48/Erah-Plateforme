<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\UpdateGiftQuickStatusRequest;
use App\Http\Requests\Web\Admin\UpdateGiftQuickStockRequest;
use App\Http\Requests\Web\Admin\UpdateShopItemQuickStatusRequest;
use App\Http\Requests\Web\Admin\UpdateShopItemQuickStockRequest;
use App\Models\Gift;
use App\Models\ShopItem;
use App\Services\Admin\AdminQuickActionService;
use App\Services\AdminOperationsCockpitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class AdminOperationsController extends Controller
{
    public function live(Request $request, AdminOperationsCockpitService $cockpitService): JsonResponse
    {
        $payload = $cockpitService->livePayload($request->query());

        return response()->json([
            ...$payload,
            'alerts_html' => view('pages.admin.partials.operations-alerts', [
                'alerts' => $payload['alerts'],
            ])->render(),
            'pending_html' => view('pages.admin.partials.operations-pending', [
                'pending' => $payload['pending'],
            ])->render(),
            'feed_rows_html' => view('pages.admin.partials.operations-feed-rows', [
                'feedItems' => $payload['feed_items'],
            ])->render(),
        ]);
    }

    public function updateGiftStatus(
        UpdateGiftQuickStatusRequest $request,
        int $giftId,
        AdminQuickActionService $quickActionService
    ): RedirectResponse {
        $gift = Gift::query()->findOrFail($giftId);
        $isActive = $request->boolean('is_active');

        $quickActionService->setGiftStatus($request->user(), $gift, $isActive);

        return back()->with('success', 'Statut du cadeau mis a jour.');
    }

    public function updateGiftStock(
        UpdateGiftQuickStockRequest $request,
        int $giftId,
        AdminQuickActionService $quickActionService
    ): RedirectResponse {
        $gift = Gift::query()->findOrFail($giftId);
        $stock = (int) $request->validated('stock');

        $quickActionService->updateGiftStock($request->user(), $gift, $stock);

        return back()->with('success', 'Stock cadeau corrige.');
    }

    public function updateShopItemStatus(
        UpdateShopItemQuickStatusRequest $request,
        int $shopItemId,
        AdminQuickActionService $quickActionService
    ): RedirectResponse {
        $item = ShopItem::query()->findOrFail($shopItemId);
        $isActive = $request->boolean('is_active');

        $quickActionService->setShopItemStatus($request->user(), $item, $isActive);

        return back()->with('success', 'Statut article shop mis a jour.');
    }

    public function updateShopItemStock(
        UpdateShopItemQuickStockRequest $request,
        int $shopItemId,
        AdminQuickActionService $quickActionService
    ): RedirectResponse {
        $item = ShopItem::query()->findOrFail($shopItemId);
        $stock = (int) $request->validated('stock');

        $quickActionService->updateShopItemStock($request->user(), $item, $stock);

        return back()->with('success', 'Stock article shop corrige.');
    }
}
