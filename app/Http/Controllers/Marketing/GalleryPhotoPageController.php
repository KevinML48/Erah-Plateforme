<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\GalleryPhoto;
use App\Services\GalleryPhotoImportService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GalleryPhotoPageController extends Controller
{
    public function __invoke(GalleryPhotoImportService $galleryPhotoImportService): View
    {
        $payload = Cache::remember('marketing.gallery-photos.payload', now()->addMinutes(5), function () use ($galleryPhotoImportService): array {
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

            return [
                'photos' => $photos,
                'filters' => $filters,
            ];
        });

        return view('marketing.galerie-photos', [
            'photos' => $payload['photos'],
            'filters' => $payload['filters'],
            'initialVisibleCount' => 12,
        ]);
    }
}
