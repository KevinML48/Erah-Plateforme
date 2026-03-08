<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\ModerateClubReviewRequest;
use App\Models\ClubReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ClubReviewAdminController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');
        $source = (string) $request->query('source', 'all');
        $sort = (string) $request->query('sort', 'latest');

        if (! in_array($status, array_merge(['all'], ClubReview::statuses()), true)) {
            $status = 'all';
        }

        if (! in_array($source, array_merge(['all'], array_keys(ClubReview::sourceLabels())), true)) {
            $source = 'all';
        }

        if (! in_array($sort, ['latest', 'oldest', 'published'], true)) {
            $sort = 'latest';
        }

        if (! $this->isReady()) {
            return view('pages.admin.reviews.index', [
                'reviews' => new LengthAwarePaginator(Collection::make(), 0, 20, 1, ['path' => $request->url()]),
                'search' => $search,
                'status' => $status,
                'source' => $source,
                'sort' => $sort,
                'statusLabels' => ClubReview::statusLabels(),
                'sourceLabels' => ClubReview::sourceLabels(),
                'stats' => [
                    'total' => 0,
                    'published' => 0,
                    'hidden' => 0,
                    'members' => 0,
                ],
            ]);
        }

        $reviews = ClubReview::query()
            ->with(['user.progress.league'])
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->when($source !== 'all', fn ($query) => $query->where('source', $source))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($nested) use ($search): void {
                    $nested
                        ->where('author_name', 'like', '%'.$search.'%')
                        ->orWhere('content', 'like', '%'.$search.'%')
                        ->orWhereHas('user', function ($userQuery) use ($search): void {
                            $userQuery
                                ->where('name', 'like', '%'.$search.'%')
                                ->orWhere('email', 'like', '%'.$search.'%');
                        });
                });
            });

        match ($sort) {
            'oldest' => $reviews->orderBy('created_at')->orderBy('id'),
            'published' => $reviews->orderByDesc('published_at')->orderByDesc('id'),
            default => $reviews->orderByDesc('created_at')->orderByDesc('id'),
        };

        $reviews = $reviews->paginate(20)->withQueryString();

        $statsBaseQuery = ClubReview::query();

        return view('pages.admin.reviews.index', [
            'reviews' => $reviews,
            'search' => $search,
            'status' => $status,
            'source' => $source,
            'sort' => $sort,
            'statusLabels' => ClubReview::statusLabels(),
            'sourceLabels' => ClubReview::sourceLabels(),
            'stats' => [
                'total' => (clone $statsBaseQuery)->count(),
                'published' => (clone $statsBaseQuery)->where('status', ClubReview::STATUS_PUBLISHED)->count(),
                'hidden' => (clone $statsBaseQuery)->where('status', ClubReview::STATUS_HIDDEN)->count(),
                'members' => (clone $statsBaseQuery)->where('source', ClubReview::SOURCE_MEMBER)->count(),
            ],
        ]);
    }

    public function update(ModerateClubReviewRequest $request, int $reviewId): RedirectResponse
    {
        if (! $this->isReady()) {
            return back()->with('error', 'Le module avis n est pas encore migre. Lancez php artisan migrate.');
        }

        $review = ClubReview::query()->findOrFail($reviewId);
        $status = $request->validated('status');

        $review->forceFill([
            'status' => $status,
            'is_featured' => $request->boolean('is_featured'),
        ]);

        if ($status === ClubReview::STATUS_PUBLISHED) {
            $review->published_at = now();
        }

        $review->save();

        return back()->with('success', 'Avis mis a jour.');
    }

    public function destroy(int $reviewId): RedirectResponse
    {
        if (! $this->isReady()) {
            return back()->with('error', 'Le module avis n est pas encore migre. Lancez php artisan migrate.');
        }

        $review = ClubReview::query()->findOrFail($reviewId);
        $review->delete();

        return back()->with('success', 'Avis supprime.');
    }

    private function isReady(): bool
    {
        return Schema::hasTable('club_reviews');
    }
}
