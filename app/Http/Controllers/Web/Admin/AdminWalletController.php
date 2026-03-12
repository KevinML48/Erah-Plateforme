<?php

namespace App\Http\Controllers\Web\Admin;

use App\Application\Actions\Bets\GrantWalletAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\GrantWalletRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class AdminWalletController extends Controller
{
    public function create(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->with('rewardWallet:user_id,balance')
            ->orderBy('name')
            ->limit(80)
            ->get(['id', 'name', 'email']);

        return view('pages.admin.wallets.grant', [
            'users' => $users,
            'search' => $search,
        ]);
    }

    public function store(
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
                mirrorLegacyBetLedger: false,
            );
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        if ($result['idempotent']) {
            return back()->with('success', 'Cet ajustement a deja ete applique.');
        }

        return back()->with('success', 'Solde mis a jour. Nouveau total: '.$result['wallet_balance'].' points plateforme.');
    }
}
