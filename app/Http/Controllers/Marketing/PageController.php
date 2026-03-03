<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View as ViewFactory;

class PageController extends Controller
{
    public function __invoke(string $slug): View
    {
        $normalizedSlug = Str::lower($slug);
        $view = 'marketing.'.$normalizedSlug;

        if (!ViewFactory::exists($view)) {
            $fallbackView = 'marketing.'.$slug;
            abort_unless(ViewFactory::exists($fallbackView), 404);

            return view($fallbackView);
        }

        return view($view);
    }
}
