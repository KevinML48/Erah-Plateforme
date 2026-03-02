<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\UserWallet;
use App\Models\WalletTransaction;
use Illuminate\View\View;

class WalletPageController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $wallet = UserWallet::query()->firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => (int) config('betting.wallet.initial_balance', 1000)]
        );

        $transactions = WalletTransaction::query()
            ->where('user_id', $user->id)
            ->latest('created_at')
            ->paginate(20);

        return view('pages.wallet.index', [
            'wallet' => $wallet,
            'transactions' => $transactions,
        ]);
    }
}
