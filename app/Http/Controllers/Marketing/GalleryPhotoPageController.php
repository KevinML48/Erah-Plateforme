<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\GalleryPhoto;
use App\Services\GalleryPhotoImportService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GalleryPhotoPageController extends Controller
{
    public function __invoke(GalleryPhotoImportService $galleryPhotoImportService): View
    {
        $galleryPhotoImportService->importIfEmpty();

        $photos = Schema::hasTable('gallery_photos')
            ? GalleryPhoto::query()->visible()->get()
            : collect();

        $filters = $photos
            ->filter(fn (GalleryPhoto $photo): bool => filled($photo->filter_key))
            ->groupBy('filter_key')
            ->map(function ($items, string $filterKey): array {
                /** @var GalleryPhoto $first */
                $first = $items->first();

                return [
                    'key' => $filterKey,
                    'label' => $first->filter_label ?: Str::headline($filterKey),
                ];
            })
            ->values();

        return view('marketing.galerie-photos', [
            'photos' => $photos,
            'filters' => $filters,
            'initialVisibleCount' => 12,
        ]);
    }
}
