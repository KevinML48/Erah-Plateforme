<?php

namespace App\Http\Controllers\Web;

use App\Application\Actions\Duels\AcceptDuelAction;
use App\Application\Actions\Duels\CreateDuelAction;
use App\Application\Actions\Duels\RefuseDuelAction;
use App\Http\Controllers\Controller;
use App\Models\Duel;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class DuelsPageController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->query('status', 'pending');

        $duels = Duel::query()
            ->forUser(auth()->id())
            ->when($tab === 'pending', fn ($query) => $query->where('status', Duel::STATUS_PENDING))
            ->when($tab === 'active', fn ($query) => $query->where('status', Duel::STATUS_ACCEPTED))
            ->when($tab === 'finished', fn ($query) => $query->whereIn('status', [Duel::STATUS_REFUSED, Duel::STATUS_EXPIRED]))
            ->with(['challenger:id,name', 'challenged:id,name'])
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('pages.duels.index', [
            'duels' => $duels,
            'status' => $tab,
        ]);
    }

    public function create(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $users = User::query()
            ->where('id', '!=', auth()->id())
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('name')
            ->limit(12)
            ->get(['id', 'name', 'email']);

        return view('pages.duels.create', [
            'users' => $users,
            'search' => $search,
        ]);
    }

    public function store(Request $request, CreateDuelAction $createDuelAction): RedirectResponse
    {
        $request->merge([
            'auth_user_id' => auth()->id(),
        ]);

        $validated = $request->validate([
            'challenged_user_id' => ['required', 'integer', 'exists:users,id', 'different:auth_user_id'],
            'idempotency_key' => ['required', 'string', 'min:8', 'max:120', 'regex:/^[A-Za-z0-9._:-]+$/'],
            'message' => ['nullable', 'string', 'max:1000'],
            'expires_in_minutes' => ['nullable', 'integer', 'min:1', 'max:10080'],
        ]);

        try {
            $createDuelAction->execute(
                challenger: auth()->user(),
                challengedUserId: (int) $validated['challenged_user_id'],
                idempotencyKey: $validated['idempotency_key'],
                message: $validated['message'] ?? null,
                expiresInMinutes: (int) ($validated['expires_in_minutes'] ?? 60),
            );
        } catch (ModelNotFoundException) {
            return back()->with('error', 'Utilisateur challenge introuvable.');
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()->route('duels.index')
            ->with('success', 'Duel cree.');
    }

    public function accept(int $duelId, AcceptDuelAction $acceptDuelAction): RedirectResponse
    {
        try {
            $acceptDuelAction->execute(auth()->user(), $duelId);
        } catch (AuthorizationException $exception) {
            return back()->with('error', $exception->getMessage());
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Duel accepte.');
    }

    public function refuse(int $duelId, RefuseDuelAction $refuseDuelAction): RedirectResponse
    {
        try {
            $refuseDuelAction->execute(auth()->user(), $duelId);
        } catch (AuthorizationException $exception) {
            return back()->with('error', $exception->getMessage());
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Duel refuse.');
    }
}
