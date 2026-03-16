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
        $tab = (string) $request->query('status', 'pending');
        if (! in_array($tab, ['pending', 'active', 'finished'], true)) {
            $tab = 'pending';
        }

        $userId = (int) auth()->id();

        $baseQuery = Duel::query()->forUser($userId);
        $statusCounts = [
            'pending' => (clone $baseQuery)
                ->where('status', Duel::STATUS_PENDING)
                ->count(),
            'active' => (clone $baseQuery)
                ->where('status', Duel::STATUS_ACCEPTED)
                ->count(),
            'finished' => (clone $baseQuery)
                ->whereIn('status', [Duel::STATUS_REFUSED, Duel::STATUS_EXPIRED])
                ->count(),
        ];

        $summary = [
            'needs_response' => (clone $baseQuery)
                ->where('status', Duel::STATUS_PENDING)
                ->where('challenged_id', $userId)
                ->count(),
            'sent_pending' => (clone $baseQuery)
                ->where('status', Duel::STATUS_PENDING)
                ->where('challenger_id', $userId)
                ->count(),
            'all' => array_sum($statusCounts),
        ];

        $duels = Duel::query()
            ->forUser($userId)
            ->when($tab === 'pending', fn ($query) => $query->where('status', Duel::STATUS_PENDING))
            ->when($tab === 'active', fn ($query) => $query->where('status', Duel::STATUS_ACCEPTED))
            ->when($tab === 'finished', fn ($query) => $query->whereIn('status', [Duel::STATUS_REFUSED, Duel::STATUS_EXPIRED]))
            ->with(['challenger:id,name,avatar_path,provider_avatar_url', 'challenged:id,name,avatar_path,provider_avatar_url'])
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $duelCards = $duels->through(fn (Duel $duel): array => $this->mapDuelCard($duel, $userId));

        return view('pages.duels.index', [
            'duels' => $duelCards,
            'status' => $tab,
            'statusCounts' => $statusCounts,
            'summary' => $summary,
        ]);
    }

    public function create(Request $request): View
    {
        $userId = (int) auth()->id();
        $search = trim((string) $request->query('q', ''));

        $users = User::query()
            ->where('id', '!=', $userId)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('name')
            ->limit(12)
            ->get(['id', 'name', 'email', 'avatar_path', 'provider_avatar_url']);

        $candidateIds = $users->pluck('id');
        $latestByUserId = [];

        if ($candidateIds->isNotEmpty()) {
            $latestDuels = Duel::query()
                ->where(function ($query) use ($userId, $candidateIds): void {
                    $query->where('challenger_id', $userId)
                        ->whereIn('challenged_id', $candidateIds);
                })
                ->orWhere(function ($query) use ($userId, $candidateIds): void {
                    $query->where('challenged_id', $userId)
                        ->whereIn('challenger_id', $candidateIds);
                })
                ->orderByDesc('id')
                ->get(['id', 'challenger_id', 'challenged_id', 'status', 'created_at']);

            foreach ($latestDuels as $duel) {
                $opponentId = (int) ($duel->challenger_id === $userId ? $duel->challenged_id : $duel->challenger_id);
                if (isset($latestByUserId[$opponentId])) {
                    continue;
                }

                $latestByUserId[$opponentId] = [
                    'status' => (string) $duel->status,
                    'status_label' => $this->duelStatusLabel((string) $duel->status),
                    'role_label' => (int) $duel->challenger_id === $userId ? 'Vous avez lance' : 'Vous avez recu',
                    'created_at' => $duel->created_at,
                ];
            }
        }

        $availableUsersCount = User::query()
            ->where('id', '!=', $userId)
            ->count();

        return view('pages.duels.create', [
            'users' => $users,
            'search' => $search,
            'latestByUserId' => $latestByUserId,
            'availableUsersCount' => $availableUsersCount,
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

    /**
     * @return array{
     *     id: int,
     *     title: string,
     *     role_label: string,
     *     opponent_name: string,
     *     status: string,
     *     status_label: string,
     *     status_class: string,
     *     message: string,
     *     created_at: ?\Illuminate\Support\Carbon,
     *     expires_at: ?\Illuminate\Support\Carbon,
     *     expires_label: string,
     *     can_respond: bool,
     *     cover_image: string
     * }
     */
    private function mapDuelCard(Duel $duel, int $userId): array
    {
        $isChallenger = (int) $duel->challenger_id === $userId;
        $opponent = $isChallenger ? $duel->challenged : $duel->challenger;
        $opponentId = (int) ($opponent?->id ?? 0);
        $opponentName = (string) ($opponent?->name ?? 'Adversaire inconnu');
        $avatarFallback = MediaStorage::fallbackAvatarUrl();
        $opponentAvatar = (string) ($opponent?->display_avatar_url ?? '');
        if ($opponentAvatar === '') {
            $opponentAvatar = $avatarFallback;
        }

        $statusClass = match ($duel->status) {
            Duel::STATUS_ACCEPTED => 'is-active',
            Duel::STATUS_REFUSED => 'is-refused',
            Duel::STATUS_EXPIRED => 'is-expired',
            default => 'is-pending',
        };

        $expiresLabel = $duel->expires_at
            ? ((now()->greaterThan($duel->expires_at))
                ? 'Expire le '.optional($duel->expires_at)->format('d/m/Y H:i')
                : 'Expire '.$duel->expires_at->diffForHumans())
            : 'Sans expiration';

        return [
            'id' => (int) $duel->id,
            'title' => 'Duel #'.(int) $duel->id,
            'role_label' => $isChallenger ? 'Lance par vous' : 'Recu',
            'opponent_name' => $opponentName,
            'status' => (string) $duel->status,
            'status_label' => $this->duelStatusLabel((string) $duel->status),
            'status_class' => $statusClass,
            'message' => trim((string) ($duel->message ?? '')),
            'created_at' => $duel->created_at,
            'expires_at' => $duel->expires_at,
            'expires_label' => $expiresLabel,
            'can_respond' => $duel->status === Duel::STATUS_PENDING && (int) $duel->challenged_id === $userId,
            'cover_image' => $opponentAvatar,
            'opponent_id' => $opponentId,
        ];
    }

    private function duelStatusLabel(string $status): string
    {
        return match ($status) {
            Duel::STATUS_ACCEPTED => 'Actif',
            Duel::STATUS_REFUSED => 'Refuse',
            Duel::STATUS_EXPIRED => 'Expire',
            default => 'En attente',
        };
    }
}
