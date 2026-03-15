<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\GalleryVideo;
use App\Services\GalleryVideoImportService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;

class GalleryVideoPageController extends Controller
{
    public function __invoke(GalleryVideoImportService $galleryVideoImportService): View
    {
        $galleryVideoImportService->importIfEmpty();

        $videos = Schema::hasTable('gallery_videos')
            ? GalleryVideo::query()->publishedVisible()->get()
            : collect();

        return view('marketing.galerie-video', [
            'videos' => $videos,
        ]);
    }
}