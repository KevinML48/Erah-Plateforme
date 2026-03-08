<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ClubReviewPresenter;
use Illuminate\View\View;

class ClubReviewPageController extends Controller
{
    public function index(ClubReviewPresenter $clubReviewPresenter): View
    {
        $reviews = $clubReviewPresenter->paginatePublished(12);

        return view('pages.reviews.index', [
            'reviews' => $reviews,
            'publishedCount' => $clubReviewPresenter->publishedCount(),
        ]);
    }
}
