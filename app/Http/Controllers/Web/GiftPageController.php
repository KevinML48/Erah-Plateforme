<?php

namespace App\Http\Controllers\Web;

use App\Application\Actions\Rewards\RedeemGiftAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\RedeemGiftRequest;
use App\Models\Gift;
use App\Models\GiftRedemption;
use App\Models\RewardWalletTransaction;
use App\Models\UserRewardWallet;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class GiftPageController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $wallet = UserRewardWallet::query()->firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        $gifts = Gift::query()
            ->where('is_active', true)
            ->orderBy('cost_points')
            ->paginate(18);

        $recentRedemptions = GiftRedemption::query()
            ->where('user_id', $user->id)
            ->with('gift')
            ->latest('requested_at')
            ->limit(5)
            ->get();

        return view('pages.gifts.index', [
            'wallet' => $wallet,
            'gifts' => $gifts,
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
}

