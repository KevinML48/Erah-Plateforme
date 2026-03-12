<?php

namespace App\Http\Controllers\TestConsole;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Console\UpdateUserRoleRequest;
use App\Models\Bet;
use App\Models\Duel;
use App\Models\GiftRedemption;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UsersConsoleController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $selectedUserId = $request->query('user_id');

        $users = User::query()
            ->with(['progress.league', 'wallet', 'rewardWallet'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($builder) use ($search): void {
                    $builder
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('id')
            ->paginate(25)
            ->withQueryString();

        $selectedUser = null;
        if ($selectedUserId) {
            $selectedUser = User::query()
                ->with(['progress.league', 'wallet', 'rewardWallet'])
                ->find($selectedUserId);
        }

        return view('pages.users.index', [
            'users' => $users,
            'search' => $search,
            'selectedUser' => $selectedUser,
        ]);
    }

    public function updateRole(UpdateUserRoleRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = User::query()->findOrFail((int) $validated['user_id']);
        $user->role = (string) $validated['role'];
        $user->save();

        return back()->with('success', 'Role utilisateur mis a jour.');
    }

    public function show(int $userId): View
    {
        $user = User::query()
            ->with([
                'progress.league',
                'wallet',
                'rewardWallet',
                'loginStreak',
                'supportSubscriptions' => fn ($query) => $query->latest('id')->limit(1),
            ])
            ->findOrFail($userId);

        $redemptions = GiftRedemption::query()
            ->where('user_id', $user->id)
            ->with('gift:id,title')
            ->orderByDesc('requested_at')
            ->limit(12)
            ->get();

        $redemptionStatusCounts = GiftRedemption::query()
            ->where('user_id', $user->id)
            ->select('status', DB::raw('count(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $bets = Bet::query()
            ->where('user_id', $user->id)
            ->with('match:id,home_team,away_team,starts_at,status,result')
            ->latest('id')
            ->limit(10)
            ->get();

        $duels = Duel::query()
            ->forUser($user->id)
            ->with(['challenger:id,name', 'challenged:id,name'])
            ->latest('id')
            ->limit(10)
            ->get();

        $activityEvents = $user->activityEvents()
            ->latest('occurred_at')
            ->limit(12)
            ->get();

        return view('pages.admin.users.show', [
            'userProfile' => $user,
            'redemptions' => $redemptions,
            'redemptionStatusCounts' => $redemptionStatusCounts,
            'redemptionStatusLabels' => GiftRedemption::statusLabels(),
            'bets' => $bets,
            'duels' => $duels,
            'activityEvents' => $activityEvents,
        ]);
    }
}
