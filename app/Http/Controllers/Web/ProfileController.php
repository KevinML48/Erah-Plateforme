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
use App\Support\MediaStorage;
use App\Services\ExperienceService;
use App\Services\MissionEngine;
use App\Services\RankService;
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
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __invoke(
        EnsureUserProgressAction $ensureUserProgressAction,
        ShortcutService $shortcutService,
        SupporterAccessResolver $supporterAccessResolver,
        MissionCatalogService $missionCatalogService,
        ExperienceService $experienceService,
        RankService $rankService,
        ProfileCosmeticService $profileCosmeticService,
    ): View
    {
        /** @var User $user */
        $user = Auth::user();
        $progress = $ensureUserProgressAction->execute($user)->load('league');
        $experience = $experienceService->summaryFor($user);

        $transactions = PointsTransaction::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->limit(5)
            ->get();
        $recentXpEntries = $this->buildRecentXpEntries($user);
        $rankOverview = $this->buildRankOverview($experience, $progress, $rankService);

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
            'recentXpEntries' => $recentXpEntries,
            'rankOverview' => $rankOverview,
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
            $newAvatarPath = MediaStorage::store($request->file('avatar'), 'avatars');
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
            MediaStorage::delete((string) $oldAvatarPath);
        }

        if ($profileCompletion >= 75) {
            $missionEngine->recordEvent($user->fresh(), 'profile.completed', 1, [
                'event_key' => 'profile.completed.'.$user->id,
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

        MediaStorage::delete($avatarPath);

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

    /**
     * @return array<int, array{title: string, points: int, total_after: int, earned_at: string}>
     */
    private function buildRecentXpEntries(User $user): array
    {
        return PointsTransaction::query()
            ->where('user_id', $user->id)
            ->where('kind', PointsTransaction::KIND_XP)
            ->latest('id')
            ->limit(4)
            ->get()
            ->map(function (PointsTransaction $transaction): array {
                return [
                    'title' => $this->describeXpSourceType((string) $transaction->source_type),
                    'points' => (int) $transaction->points,
                    'total_after' => (int) ($transaction->after_xp ?? 0),
                    'earned_at' => (string) optional($transaction->created_at)->format('d/m/Y H:i'),
                ];
            })
            ->all();
    }

    /**
     * @param array<string, mixed> $experience
     * @return array{
     *     current_rank_name: string,
     *     current_rank_threshold: int,
     *     next_rank_name: string|null,
     *     next_rank_threshold: int|null,
     *     xp_to_next_rank: int,
     *     rank_points: int,
     *     duel_score: int,
     *     duel_best_streak: int,
     *     last_points_at: string|null,
     *     has_next_rank: bool
     * }
     */
    private function buildRankOverview(array $experience, $progress, RankService $rankService): array
    {
        $currentRankKey = (string) data_get($experience, 'rank.key', 'bronze');
        $totalXp = (int) data_get($experience, 'total_xp', 0);

        $nextRank = $rankService->definitions()
            ->first(fn (array $definition): bool => (int) $definition['xp_threshold'] > $totalXp);

        return [
            'current_rank_name' => (string) data_get($experience, 'rank.name', 'Bronze'),
            'current_rank_threshold' => (int) data_get($experience, 'rank.xp_threshold', 0),
            'next_rank_name' => is_array($nextRank) ? (string) ($nextRank['name'] ?? '') : null,
            'next_rank_threshold' => is_array($nextRank) ? (int) ($nextRank['xp_threshold'] ?? 0) : null,
            'xp_to_next_rank' => is_array($nextRank)
                ? max(0, ((int) ($nextRank['xp_threshold'] ?? 0)) - $totalXp)
                : 0,
            'rank_points' => (int) ($progress->total_rank_points ?? 0),
            'duel_score' => (int) ($progress->duel_score ?? 0),
            'duel_best_streak' => (int) ($progress->duel_best_streak ?? 0),
            'last_points_at' => $progress->last_points_at?->format('d/m/Y H:i'),
            'has_next_rank' => is_array($nextRank) && ((string) ($nextRank['key'] ?? '')) !== $currentRankKey,
        ];
    }

    private function describeXpSourceType(string $sourceType): string
    {
        return match ($sourceType) {
            'admin_grant' => 'Attribution manuelle',
            'community.missions.daily_bonus' => 'Mission completee',
            'community.duels.win' => 'Duel remporte',
            'community.duels.loss' => 'Duel dispute',
            'community.bets.win' => 'Pari gagne',
            'community.bets.loss' => 'Pari termine',
            'community.clips.view' => 'Clip regarde',
            'community.clips.like' => 'Clip like',
            'community.clips.comment' => 'Commentaire clip',
            'community.clips.share' => 'Clip partage',
            'community.streak.login' => 'Connexion quotidienne',
            default => trim(ucwords(str_replace(['community.', '.', '_', ':'], ['', ' ', ' ', ' '], $sourceType))),
        };
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
            $user->hasAnyAvatar(),
            ! blank($user->twitter_url)
                || ! blank($user->instagram_url)
                || ! blank($user->tiktok_url)
                || ! blank($user->discord_url)
                || $user->socialAccounts()->where('provider', 'discord')->exists(),
        ];

        $completed = count(array_filter($checkpoints));

        return (int) round(($completed / max(1, count($checkpoints))) * 100);
    }
}
