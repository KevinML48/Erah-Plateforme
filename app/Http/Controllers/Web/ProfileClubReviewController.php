<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\UpsertClubReviewRequest;
use App\Models\ClubReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProfileClubReviewController extends Controller
{
    public function store(UpsertClubReviewRequest $request): RedirectResponse
    {
        return $this->persist($request, true);
    }

    public function update(UpsertClubReviewRequest $request): RedirectResponse
    {
        return $this->persist($request, false);
    }

    public function destroy(Request $request): RedirectResponse
    {
        if (! $this->isReady()) {
            return back()->with('error', 'Le module avis n est pas encore migre. Lancez php artisan migrate.');
        }

        $review = $request->user()->clubReview;

        if (! $review) {
            return back()->with('success', 'Aucun avis a retirer.');
        }

        $review->forceFill([
            'status' => ClubReview::STATUS_HIDDEN,
            'is_featured' => false,
        ])->save();

        return back()->with('success', 'Votre avis a ete retire de l espace public.');
    }

    private function persist(UpsertClubReviewRequest $request, bool $isCreate): RedirectResponse
    {
        if (! $this->isReady()) {
            return back()->with('error', 'Le module avis n est pas encore migre. Lancez php artisan migrate.');
        }

        $user = $request->user();
        $review = $user->clubReview()->firstOrNew();

        $review->forceFill([
            'content' => $request->validated('content'),
            'status' => ClubReview::STATUS_PUBLISHED,
            'source' => ClubReview::SOURCE_MEMBER,
            'author_name' => null,
            'author_profile_url' => null,
            'published_at' => now(),
        ]);

        if (! $review->exists) {
            $review->user()->associate($user);
        }

        $review->save();

        return back()->with('success', $isCreate && $review->wasRecentlyCreated
            ? 'Votre avis a ete publie.'
            : 'Votre avis a ete mis a jour.');
    }

    private function isReady(): bool
    {
        return Schema::hasTable('club_reviews');
    }
}
