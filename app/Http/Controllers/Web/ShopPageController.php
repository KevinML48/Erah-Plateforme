<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
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

    public function purchase(int $shopItemId, ShopService $shopService): RedirectResponse
    {
        $item = ShopItem::query()->active()->findOrFail($shopItemId);

        try {
            $shopService->purchase(auth()->user(), $item);
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Achat valide: '.$item->name.'.');
    }
}
