<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\UserWallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WalletPageController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();

        $wallet = UserWallet::query()->firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => (int) config('betting.wallet.initial_balance', 1000)]
        );

        $allowedTypes = [
            WalletTransaction::TYPE_STAKE,
            WalletTransaction::TYPE_REFUND,
            WalletTransaction::TYPE_PAYOUT,
            WalletTransaction::TYPE_GRANT,
            WalletTransaction::TYPE_ADJUST,
            WalletTransaction::TYPE_VOID_REFUND,
        ];
        $allowedDirections = ['all', 'in', 'out'];

        $type = (string) $request->query('type', 'all');
        if ($type !== 'all' && ! in_array($type, $allowedTypes, true)) {
            $type = 'all';
        }

        $direction = (string) $request->query('direction', 'all');
        if (! in_array($direction, $allowedDirections, true)) {
            $direction = 'all';
        }

        $search = trim((string) $request->query('q', ''));
        $search = function_exists('mb_substr')
            ? mb_substr($search, 0, 80)
            : substr($search, 0, 80);

        $baseQuery = WalletTransaction::query()
            ->where('user_id', $user->id);

        $query = clone $baseQuery;
        if ($type !== 'all') {
            $query->where('type', $type);
        }

        if ($direction === 'in') {
            $query->where('amount', '>', 0);
        } elseif ($direction === 'out') {
            $query->where('amount', '<', 0);
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('type', 'like', '%'.$search.'%')
                    ->orWhere('ref_type', 'like', '%'.$search.'%')
                    ->orWhere('unique_key', 'like', '%'.$search.'%');
            });
        }

        $transactions = (clone $query)
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        $totalCount = (clone $baseQuery)->count();
        $inCount = (clone $baseQuery)->where('amount', '>', 0)->count();
        $outCount = (clone $baseQuery)->where('amount', '<', 0)->count();
        $inTotal = (int) (clone $baseQuery)->where('amount', '>', 0)->sum('amount');
        $outTotal = (int) abs((int) (clone $baseQuery)->where('amount', '<', 0)->sum('amount'));
        $monthStart = now()->startOfMonth();
        $monthTransactions = (clone $baseQuery)->where('created_at', '>=', $monthStart)->count();

        $typeCounts = (clone $baseQuery)
            ->selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        return view('pages.wallet.index', [
            'wallet' => $wallet,
            'transactions' => $transactions,
            'filters' => [
                'type' => $type,
                'direction' => $direction,
                'q' => $search,
            ],
            'summary' => [
                'total' => (int) $totalCount,
                'in_count' => (int) $inCount,
                'out_count' => (int) $outCount,
                'in_total' => (int) $inTotal,
                'out_total' => (int) $outTotal,
                'filtered' => (int) ((clone $query)->count()),
                'month_count' => (int) $monthTransactions,
            ],
            'typeCounts' => $typeCounts,
        ]);
    }
}
