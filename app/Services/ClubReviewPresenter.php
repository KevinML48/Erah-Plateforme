<?php

namespace App\Services;

use App\Models\ClubReview;
use App\Models\UserProgress;
use App\Support\ClubReviewCatalog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as PaginationLengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ClubReviewPresenter
{
    public function __construct(
        private readonly SupporterAccessResolver $supporterAccessResolver
    ) {
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function latestPublished(int $limit = 5): Collection
    {
        if (! $this->isReady()) {
            return ClubReviewCatalog::fallbackPresentedReviews($limit);
        }

        $reviews = ClubReview::query()
            ->with($this->relations())
            ->visibleOnHome()
            ->limit(max(1, $limit))
            ->get();

        $presented = $this->presentCollection($reviews);

        if ($presented->count() >= $limit) {
            return $presented;
        }

        return $this->appendFallbackReviews($presented, $limit);
    }

    public function paginatePublished(int $perPage = 12): LengthAwarePaginator
    {
        if (! $this->isReady()) {
            return $this->fallbackPaginator($perPage);
        }

        $publishedCount = ClubReview::query()->published()->count();

        if ($publishedCount === 0) {
            return $this->fallbackPaginator($perPage);
        }

        $paginator = ClubReview::query()
            ->with($this->relations())
            ->visibleOnHome()
            ->paginate(max(1, $perPage))
            ->withQueryString();

        $paginator->setCollection($this->presentCollection($paginator->getCollection()));

        return $paginator;
    }

    public function publishedCount(): int
    {
        if (! $this->isReady()) {
            return ClubReviewCatalog::fallbackPresentedReviews()->count();
        }

        $count = ClubReview::query()->published()->count();

        return $count > 0 ? $count : ClubReviewCatalog::fallbackPresentedReviews()->count();
    }

    public function isReady(): bool
    {
        return Schema::hasTable('club_reviews');
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $reviews
     * @return Collection<int, array<string, mixed>>
     */
    private function appendFallbackReviews(Collection $reviews, int $limit): Collection
    {
        $existingAuthors = $reviews
            ->pluck('author_name')
            ->filter()
            ->map(fn ($name) => Str::lower((string) $name))
            ->all();

        $fallback = ClubReviewCatalog::fallbackPresentedReviews()
            ->reject(fn (array $review) => in_array(Str::lower((string) $review['author_name']), $existingAuthors, true))
            ->take(max(0, $limit - $reviews->count()));

        return $reviews->concat($fallback)->values();
    }

    private function fallbackPaginator(int $perPage): LengthAwarePaginator
    {
        $page = max(1, PaginationLengthAwarePaginator::resolveCurrentPage('page'));
        $allFallbackReviews = ClubReviewCatalog::fallbackPresentedReviews();
        $safePerPage = max(1, $perPage);

        return new PaginationLengthAwarePaginator(
            $allFallbackReviews->forPage($page, $safePerPage)->values(),
            $allFallbackReviews->count(),
            $safePerPage,
            $page,
            ['path' => request()->url(), 'pageName' => 'page']
        );
    }

    /**
     * @param  Collection<int, ClubReview>  $reviews
     * @return Collection<int, array<string, mixed>>
     */
    public function presentCollection(Collection $reviews): Collection
    {
        $rankPositions = $this->resolveRankPositions($reviews);

        return $reviews->map(function (ClubReview $review) use ($rankPositions): array {
            $user = $review->user;
            $progress = $user?->progress;
            $supporterSummary = $user ? $this->supporterAccessResolver->summary($user) : [];
            $badges = collect([
                ($supporterSummary['is_founder'] ?? false) ? 'Supporter fondateur' : null,
                $supporterSummary['loyalty_badge'] ?? null,
            ])->filter()->values()->all();
            $meta = collect([
                $progress?->league?->name ? 'Ligue '.$progress->league->name : null,
                $progress ? number_format((int) $progress->total_rank_points, 0, ',', ' ').' pts classement' : null,
                isset($rankPositions[$user?->id ?? 0]) ? 'Classement #'.$rankPositions[$user->id] : null,
                $progress && (int) $progress->total_xp > 0 ? number_format((int) $progress->total_xp, 0, ',', ' ').' XP' : null,
            ])->filter()->values()->all();

            return [
                'id' => $review->id,
                'content' => $review->content,
                'author_name' => $review->authorDisplayName(),
                'author_url' => $user ? route('users.public', $user) : $review->author_profile_url,
                'author_cta' => $user ? 'Voir le profil public' : 'Voir la source',
                'avatar_url' => $user?->avatar_url,
                'initials' => Str::upper(Str::substr(trim($review->authorDisplayName()), 0, 2)),
                'published_at' => $review->published_at,
                'is_member' => $user !== null,
                'is_supporter' => (bool) ($supporterSummary['is_active'] ?? false),
                'supporter_label' => ($supporterSummary['is_active'] ?? false) ? 'Supporter actif' : null,
                'meta' => $meta,
                'badges' => $badges,
                'source_label' => $review->sourceLabel(),
            ];
        })->values();
    }

    /**
     * @return array<int, string|array<int, string>>
     */
    private function relations(): array
    {
        return [
            'user.progress.league',
            'user.supportPublicProfile',
        ];
    }

    /**
     * @param  Collection<int, ClubReview>  $reviews
     * @return array<int, int>
     */
    private function resolveRankPositions(Collection $reviews): array
    {
        $progressRows = $reviews
            ->pluck('user')
            ->filter()
            ->map(fn ($user) => $user->progress)
            ->filter()
            ->values();

        if ($progressRows->isEmpty()) {
            return [];
        }

        $positions = [];

        foreach ($progressRows as $progress) {
            if (! $progress instanceof UserProgress) {
                continue;
            }

            $positions[(int) $progress->user_id] = 1 + UserProgress::query()
                ->where(function ($query) use ($progress): void {
                    $query
                        ->where('total_rank_points', '>', (int) $progress->total_rank_points)
                        ->orWhere(function ($nested) use ($progress): void {
                            $nested
                                ->where('total_rank_points', (int) $progress->total_rank_points)
                                ->where('total_xp', '>', (int) $progress->total_xp);
                        })
                        ->orWhere(function ($nested) use ($progress): void {
                            $nested
                                ->where('total_rank_points', (int) $progress->total_rank_points)
                                ->where('total_xp', (int) $progress->total_xp)
                                ->where('user_id', '<', (int) $progress->user_id);
                        });
                })
                ->count();
        }

        return $positions;
    }
}
