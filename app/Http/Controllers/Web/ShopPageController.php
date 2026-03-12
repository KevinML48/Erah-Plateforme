<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\ShopPurchaseRequest;
use App\Models\ShopItem;
use App\Services\ShopService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShopPageController extends Controller
{
    public function index(): View
    {
        $items = ShopItem::query()
            ->active()
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->paginate(12)
            ->withQueryString();

        $purchases = auth()->user()
            ? auth()->user()->purchases()->with('shopItem')->latest('purchased_at')->limit(10)->get()
            : collect();

        return view('pages.shop.index', [
            'items' => $items,
            'purchases' => $purchases,
        ]);
    }

    public function purchase(int $shopItemId, ShopPurchaseRequest $request, ShopService $shopService): RedirectResponse
    {
        $item = ShopItem::query()->active()->findOrFail($shopItemId);

        try {
            $result = $shopService->purchase(
                user: auth()->user(),
                item: $item,
                idempotencyKey: (string) $request->validated('idempotency_key'),
            );
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        if ($result['idempotent']) {
            return back()->with('success', 'Achat deja enregistre (replay idempotent).');
        }

        return back()->with('success', 'Achat valide: '.$item->name.'.');
    }
}
