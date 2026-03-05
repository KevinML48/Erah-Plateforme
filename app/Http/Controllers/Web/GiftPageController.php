<?php

namespace App\Http\Controllers\Web;

use App\Application\Actions\Rewards\RedeemGiftAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\RedeemGiftRequest;
use App\Models\Gift;
use App\Models\GiftRedemption;
use App\Models\RewardWalletTransaction;
use App\Models\UserRewardWallet;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

class GiftPageController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();

        $wallet = UserRewardWallet::query()->firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        $catalog = Gift::query()
            ->where('is_active', true)
            ->orderBy('cost_points')
            ->get();

        $giftCards = $catalog
            ->map(function (Gift $gift): array {
                $categoryKey = $this->resolveCategoryKey($gift);

                return [
                    'gift' => $gift,
                    'category_key' => $categoryKey,
                    'category_label' => $this->categoryLabel($categoryKey),
                ];
            })
            ->values();

        $categories = $giftCards
            ->pluck('category_key')
            ->unique()
            ->values()
            ->map(fn (string $key): array => [
                'key' => $key,
                'label' => $this->categoryLabel($key),
            ]);

        $selectedCategory = (string) $request->query('category', 'all');

        if ($selectedCategory !== 'all' && ! $categories->pluck('key')->contains($selectedCategory)) {
            $selectedCategory = 'all';
        }

        $filteredCards = $selectedCategory === 'all'
            ? $giftCards
            : $giftCards->where('category_key', $selectedCategory)->values();

        $recentRedemptions = GiftRedemption::query()
            ->where('user_id', $user->id)
            ->with('gift')
            ->latest('requested_at')
            ->limit(5)
            ->get();

        return view('pages.gifts.index', [
            'wallet' => $wallet,
            'giftCards' => $filteredCards->values(),
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'recentRedemptions' => $recentRedemptions,
        ]);
    }

    public function show(int $giftId): View
    {
        $user = auth()->user();

        $wallet = UserRewardWallet::query()->firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        $gift = Gift::query()->findOrFail($giftId);

        $myRecentRedemptions = GiftRedemption::query()
            ->where('user_id', $user->id)
            ->where('gift_id', $gift->id)
            ->latest('requested_at')
            ->limit(10)
            ->get();

        return view('pages.gifts.show', [
            'wallet' => $wallet,
            'gift' => $gift,
            'myRecentRedemptions' => $myRecentRedemptions,
        ]);
    }

    public function redeem(
        RedeemGiftRequest $request,
        int $giftId,
        RedeemGiftAction $redeemGiftAction
    ): RedirectResponse {
        try {
            $result = $redeemGiftAction->execute(
                user: $request->user(),
                giftId: $giftId,
                idempotencyKey: (string) $request->validated('idempotency_key')
            );
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        if ($result['idempotent']) {
            return back()->with('success', 'Demande deja enregistree (replay idempotent).');
        }

        return back()->with('success', 'Demande de cadeau envoyee avec succes.');
    }

    public function redemptions(): View
    {
        $redemptions = GiftRedemption::query()
            ->where('user_id', auth()->id())
            ->with('gift')
            ->latest('requested_at')
            ->paginate(20);

        return view('pages.gifts.redemptions', [
            'redemptions' => $redemptions,
        ]);
    }

    public function wallet(): View
    {
        $user = auth()->user();

        $wallet = UserRewardWallet::query()->firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        $transactions = RewardWalletTransaction::query()
            ->where('user_id', $user->id)
            ->latest('created_at')
            ->paginate(25);

        return view('pages.gifts.wallet', [
            'wallet' => $wallet,
            'transactions' => $transactions,
        ]);
    }

    private function resolveCategoryKey(Gift $gift): string
    {
        $content = Str::lower(trim(($gift->title ?? '').' '.($gift->description ?? '')));

        if (
            Str::contains($content, ['t-shirt', 'shirt', 'mug', 'hoodie', 'casquette', 'maillot', 'merch'])
        ) {
            return 'merch';
        }

        if (
            Str::contains($content, ['ticket', 'event', 'pass', 'vip', 'billet', 'experience'])
        ) {
            return 'experience';
        }

        if (Str::contains($content, ['code', 'skin', 'bundle', 'digital'])) {
            return 'digital';
        }

        if ((int) $gift->cost_points >= 1500) {
            return 'premium';
        }

        return 'starter';
    }

    private function categoryLabel(string $key): string
    {
        return match ($key) {
            'merch' => 'Merchandising',
            'experience' => 'Experiences',
            'digital' => 'Digital',
            'premium' => 'Premium',
            default => 'Starter',
        };
    }
}
