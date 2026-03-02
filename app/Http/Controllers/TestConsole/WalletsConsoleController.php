<?php

namespace App\Http\Controllers\TestConsole;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Bets\GrantWalletAction;
use App\Application\Actions\Rewards\ApplyRewardWalletTransactionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\GrantWalletRequest;
use App\Http\Requests\Web\Console\GrantRewardWalletRequest;
use App\Models\RewardWalletTransaction;
use App\Models\User;
use App\Models\UserRewardWallet;
use App\Models\UserWallet;
use App\Models\WalletTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class WalletsConsoleController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $search = trim((string) $request->query('q', ''));

        $betWallet = UserWallet::query()->firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => (int) config('betting.wallet.initial_balance', 1000)]
        );
        $rewardWallet = UserRewardWallet::query()->firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        $betTransactions = WalletTransaction::query()
            ->where('user_id', $user->id)
            ->latest('created_at')
            ->limit(20)
            ->get();

        $rewardTransactions = RewardWalletTransaction::query()
            ->where('user_id', $user->id)
            ->latest('created_at')
            ->limit(20)
            ->get();

        $grantUsers = collect();
        if ($user->role === 'admin') {
            $grantUsers = User::query()
                ->when($search !== '', function ($query) use ($search): void {
                    $query->where(function ($builder) use ($search): void {
                        $builder
                            ->where('name', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%');
                    });
                })
                ->with(['wallet:user_id,balance', 'rewardWallet:user_id,balance'])
                ->orderBy('name')
                ->limit(150)
                ->get(['id', 'name', 'email']);
        }

        return view('pages.wallets.index', [
            'betWallet' => $betWallet,
            'rewardWallet' => $rewardWallet,
            'betTransactions' => $betTransactions,
            'rewardTransactions' => $rewardTransactions,
            'grantUsers' => $grantUsers,
            'search' => $search,
        ]);
    }

    public function grantBet(
        GrantWalletRequest $request,
        GrantWalletAction $grantWalletAction
    ): RedirectResponse {
        $validated = $request->validated();
        $targetUser = User::query()->findOrFail((int) $validated['user_id']);

        try {
            $result = $grantWalletAction->execute(
                actor: $request->user(),
                targetUser: $targetUser,
                amount: (int) $validated['amount'],
                reason: (string) $validated['reason'],
                idempotencyKey: (string) $validated['idempotency_key'],
            );
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        if ($result['idempotent']) {
            return back()->with('success', 'Grant bet_points deja applique.');
        }

        return back()->with('success', 'bet_points credites.');
    }

    public function grantReward(
        GrantRewardWalletRequest $request,
        ApplyRewardWalletTransactionAction $applyRewardWalletTransactionAction,
        StoreAuditLogAction $storeAuditLogAction
    ): RedirectResponse {
        $validated = $request->validated();
        $targetUser = User::query()->findOrFail((int) $validated['user_id']);

        try {
            $result = $applyRewardWalletTransactionAction->execute(
                user: $targetUser,
                type: RewardWalletTransaction::TYPE_GRANT,
                amount: (int) $validated['amount'],
                uniqueKey: 'console.reward.grant.'.(string) $validated['idempotency_key'],
                refType: RewardWalletTransaction::REF_TYPE_ADMIN,
                refId: (string) $request->user()->id,
                metadata: [
                    'reason' => (string) $validated['reason'],
                    'actor_id' => $request->user()->id,
                ],
                initialBalanceIfMissing: 0,
            );
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        $storeAuditLogAction->execute(
            action: 'reward_wallet.grant',
            actor: $request->user(),
            target: $targetUser,
            context: [
                'amount' => (int) $validated['amount'],
                'reason' => (string) $validated['reason'],
                'idempotency_key' => (string) $validated['idempotency_key'],
                'idempotent' => (bool) $result['idempotent'],
            ],
        );

        if ($result['idempotent']) {
            return back()->with('success', 'Grant reward_points deja applique.');
        }

        return back()->with('success', 'reward_points credites.');
    }
}

