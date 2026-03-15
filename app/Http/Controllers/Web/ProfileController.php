<?php

namespace App\Http\Controllers\Web;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Ranking\EnsureUserProgressAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\UpdateProfileRequest;
use App\Models\Bet;
use App\Models\Duel;
use App\Models\PointsTransaction;
use App\Models\User;
use App\Services\ExperienceService;
use App\Services\MissionEngine;
use App\Services\ShortcutService;
use App\Services\MissionCatalogService;
use App\Services\MissionFocusService;
use App\Services\ProfileCosmeticService;
use App\Services\SupporterAccessResolver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __invoke(
        EnsureUserProgressAction $ensureUserProgressAction,
        ShortcutService $shortcutService,
        SupporterAccessResolver $supporterAccessResolver,
        MissionCatalogService $missionCatalogService,
        ExperienceService $experienceService,
        ProfileCosmeticService $profileCosmeticService,
    ): View
    {
        $user = auth()->user();
        $progress = $ensureUserProgressAction->execute($user)->load('league');
        $experience = $experienceService->summaryFor($user);

        $transactions = PointsTransaction::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $stats = $this->buildStats($user);
        $currentShortcuts = $shortcutService->getForUser($user);
        $availableShortcuts = $shortcutService->getAvailableForUser($user);
        $supporterProfile = $supporterAccessResolver->ensurePublicProfile($user);
        $supporterSummary = $supporterAccessResolver->summary($user);
        $missionPayload = $missionCatalogService->dashboardPayload($user);
        $socialConnections = $user->socialAccounts()
            ->whereIn('provider', ['discord', 'google'])
            ->orderBy('provider')
            ->get()
            ->keyBy('provider');
        $assistantFavorites = Schema::hasTable('assistant_favorites')
            ? $user->assistantFavorites()
                ->latest('id')
                ->limit(12)
                ->get()
            : collect();
        $profileCosmetics = $profileCosmeticService->snapshotFor($user);

        return view('pages.profile.show', [
            'user' => $user,
            'progress' => $progress,
            'transactions' => $transactions,
            'stats' => $stats,
            'clubReview' => Schema::hasTable('club_reviews') ? $user->clubReview()->first() : null,
            'currentShortcuts' => $currentShortcuts,
            'availableShortcuts' => $availableShortcuts,
            'minShortcuts' => $shortcutService->minShortcuts(),
            'maxShortcuts' => $shortcutService->maxShortcuts(),
            'experience' => $experience,
            'supporterProfile' => $supporterProfile,
            'supporterSummary' => $supporterSummary,
            'missionFocusCards' => collect($missionPayload['focus']),
            'missionSummary' => $missionPayload['summary'],
            'socialConnections' => $socialConnections,
            'assistantFavorites' => $assistantFavorites,
            'profileCosmetics' => $profileCosmetics,
        ]);
    }

    public function update(
        UpdateProfileRequest $request,
        StoreAuditLogAction $storeAuditLogAction,
        MissionEngine $missionEngine
    ): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        $isSupporterActive = $user->isSupporterActive();
        $newAvatarPath = null;
        $oldAvatarPath = $user->avatar_path;
        $profileCompletion = 0;

        if ($request->hasFile('avatar')) {
            $newAvatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        DB::transaction(function () use ($user, $validated, $newAvatarPath, $storeAuditLogAction, $isSupporterActive, $request, &$profileCompletion) {
            $lockedUser = User::query()
                ->whereKey($user->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedUser->name = (string) $validated['name'];
            $lockedUser->bio = $validated['bio'] ?? null;
            $lockedUser->twitter_url = $validated['twitter_url'] ?? null;
            $lockedUser->instagram_url = $validated['instagram_url'] ?? null;
            $lockedUser->tiktok_url = $validated['tiktok_url'] ?? null;
            $lockedUser->discord_url = $validated['discord_url'] ?? null;

            if ($newAvatarPath !== null) {
                $lockedUser->avatar_path = $newAvatarPath;
            }

            $lockedUser->save();

            if (
                $isSupporterActive
                && (array_key_exists('show_in_supporter_wall', $validated) || array_key_exists('supporter_display_name', $validated))
            ) {
                $lockedUser->supportPublicProfile()->updateOrCreate(
                    ['user_id' => $lockedUser->id],
                    [
                        'is_visible_on_wall' => $request->boolean('show_in_supporter_wall'),
                        'display_name' => $validated['supporter_display_name'] ?? $lockedUser->name,
                    ],
                );
            }

            $storeAuditLogAction->execute(
                action: 'profile.updated',
                actor: $lockedUser,
                target: $lockedUser,
                context: [
                    'updated_fields' => [
                        'name',
                        'bio',
                        'twitter_url',
                        'instagram_url',
                        'tiktok_url',
                        'discord_url',
                        'avatar_path' => $newAvatarPath !== null,
                        'show_in_supporter_wall' => $isSupporterActive && array_key_exists('show_in_supporter_wall', $validated),
                        'supporter_display_name' => $isSupporterActive && array_key_exists('supporter_display_name', $validated),
                    ],
                ],
            );

            $profileCompletion = $this->calculateProfileCompletion($lockedUser);
        });

        if ($newAvatarPath !== null && ! blank($oldAvatarPath) && $oldAvatarPath !== $newAvatarPath) {
            if (! str_starts_with((string) $oldAvatarPath, 'http://') && ! str_starts_with((string) $oldAvatarPath, 'https://')) {
                Storage::disk('public')->delete((string) $oldAvatarPath);
            }
        }

        if ($profileCompletion >= 75) {
            $missionEngine->recordEvent($user->fresh(), 'profile.complèted', 1, [
                'event_key' => 'profile.complèted.'.$user->id,
                'profile_completion' => $profileCompletion,
                'subject_type' => User::class,
                'subject_id' => (string) $user->id,
            ]);
        }

        $returnRoute = $request->input('_profile_return') === 'app'
            ? 'app.profile'
            : 'profile.show';

        return redirect()
            ->route($returnRoute)
            ->with('success', 'Profil mis a jour.');
    }

    public function transactions(Request $request): View
    {
        $user = $request->user();
        $category = (string) $request->query('category', 'all');
        $kind = (string) $request->query('kind', 'all');
        $search = trim((string) $request->query('q', ''));

        $allowedCategories = ['all', 'missions', 'duels', 'paris', 'clips', 'admin', 'autres'];
        if (! in_array($category, $allowedCategories, true)) {
            $category = 'all';
        }

        $allowedKinds = ['all', PointsTransaction::KIND_XP, PointsTransaction::KIND_RANK];
        if (! in_array($kind, $allowedKinds, true)) {
            $kind = 'all';
        }

        $transactionsQuery = PointsTransaction::query()
            ->where('user_id', $user->id);

        if ($kind !== 'all') {
            $transactionsQuery->where('kind', $kind);
        }

        $this->applyCategoryFilter($transactionsQuery, $category);

        if ($search !== '') {
            $transactionsQuery->where(function (Builder $query) use ($search) {
                $query
                    ->where('source_type', 'like', '%'.$search.'%')
                    ->orWhere('source_id', 'like', '%'.$search.'%');
            });
        }

        $transactions = $transactionsQuery
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $baseQuery = PointsTransaction::query()->where('user_id', $user->id);
        $categoryCounts = [
            'all' => (clone $baseQuery)->count(),
            'missions' => $this->countByCategory((clone $baseQuery), 'missions'),
            'duels' => $this->countByCategory((clone $baseQuery), 'duels'),
            'paris' => $this->countByCategory((clone $baseQuery), 'paris'),
            'clips' => $this->countByCategory((clone $baseQuery), 'clips'),
            'admin' => $this->countByCategory((clone $baseQuery), 'admin'),
            'autres' => $this->countByCategory((clone $baseQuery), 'autres'),
        ];

        $kindCounts = [
            'all' => (clone $baseQuery)->count(),
            PointsTransaction::KIND_XP => (clone $baseQuery)->where('kind', PointsTransaction::KIND_XP)->count(),
            PointsTransaction::KIND_RANK => (clone $baseQuery)->where('kind', PointsTransaction::KIND_RANK)->count(),
        ];

        return view('pages.profile.transactions', [
            'transactions' => $transactions,
            'category' => $category,
            'kind' => $kind,
            'search' => $search,
            'categoryCounts' => $categoryCounts,
            'kindCounts' => $kindCounts,
        ]);
    }

    public function destroy(
        Request $request,
        StoreAuditLogAction $storeAuditLogAction
    ): RedirectResponse {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $avatarPath = (string) ($user->avatar_path ?? '');

        $storeAuditLogAction->execute(
            action: 'profile.deleted',
            actor: $user,
            target: $user,
            context: [
                'initiator' => 'self-service',
                'ip' => $request->ip(),
            ],
        );

        Auth::guard('web')->logout();
        $user->delete();

        if ($avatarPath !== '' && ! str_starts_with($avatarPath, 'http://') && ! str_starts_with($avatarPath, 'https://')) {
            Storage::disk('public')->delete($avatarPath);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', 'Compte supprime.');
    }

    /**
     * @return array<string, int>
     */
    private function buildStats(User $user): array
    {
        return [
            'likes' => $user->clipLikes()->count(),
            'comments' => $user->clipComments()->count(),
            'duels' => Duel::query()->forUser($user->id)->count(),
            'bets' => Bet::query()->where('user_id', $user->id)->count(),
        ];
    }

    private function countByCategory(Builder $query, string $category): int
    {
        $this->applyCategoryFilter($query, $category);

        return $query->count();
    }

    private function applyCategoryFilter(Builder $query, string $category): void
    {
        if ($category === 'all') {
            return;
        }

        if ($category === 'missions') {
            $query->where('source_type', 'like', 'mission.%');

            return;
        }

        if ($category === 'duels') {
            $query->where('source_type', 'like', 'duel.%');

            return;
        }

        if ($category === 'paris') {
            $query->where('source_type', 'like', 'bet.%');

            return;
        }

        if ($category === 'clips') {
            $query->where('source_type', 'like', 'clip.%');

            return;
        }

        if ($category === 'admin') {
            $query->where(function (Builder $adminQuery) {
                $adminQuery
                    ->where('source_type', 'admin_grant')
                    ->orWhere('source_type', 'like', 'admin.%')
                    ->orWhere('source_type', 'like', 'admin_%');
            });

            return;
        }

        if ($category === 'autres') {
            $query->where(function (Builder $otherQuery) {
                $otherQuery
                    ->where('source_type', 'not like', 'mission.%')
                    ->where('source_type', 'not like', 'duel.%')
                    ->where('source_type', 'not like', 'bet.%')
                    ->where('source_type', 'not like', 'clip.%')
                    ->where('source_type', '<>', 'admin_grant')
                    ->where('source_type', 'not like', 'admin.%')
                    ->where('source_type', 'not like', 'admin_%');
            });
        }
    }

    private function calculateProfileCompletion(User $user): int
    {
        $checkpoints = [
            ! blank($user->name),
            ! blank($user->bio),
            ! blank($user->avatar_path),
            ! blank($user->twitter_url)
                || ! blank($user->instagram_url)
                || ! blank($user->tiktok_url)
                || ! blank($user->discord_url)
                || $user->socialAccounts()->where('provider', 'discord')->exists(),
        ];

        $complèted = count(array_filter($checkpoints));

        return (int) round(($complèted / max(1, count($checkpoints))) * 100);
    }
}
