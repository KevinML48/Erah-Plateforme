<?php

namespace App\Http\Controllers\TestConsole;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Console\UpdateUserRoleRequest;
use App\Models\User;
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
}

