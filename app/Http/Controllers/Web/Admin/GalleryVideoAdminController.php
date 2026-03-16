<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\GalleryVideoUpsertRequest;
use App\Models\GalleryVideo;
use App\Services\GalleryVideoImportService;
use App\Support\MediaStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GalleryVideoAdminController extends Controller
{
    private const SORT_OPTIONS = [
        'manual' => 'Ordre manuel',
        'recent' => 'Plus recentes',
        'published' => 'Plus recemment publiees',
        'title_az' => 'Titre A-Z',
        'title_za' => 'Titre Z-A',
    ];

    public function index(Request $request, GalleryVideoImportService $galleryVideoImportService): View
    {
        $importResult = $galleryVideoImportService->importIfEmpty();

        if (! Schema::hasTable('gallery_videos')) {
            return view('pages.admin.gallery-videos.index', [
                'videos' => new LengthAwarePaginator([], 0, 12),
                'autoImportedCount' => $importResult,
                'stats' => [
                    'total' => 0,
                    'published' => 0,
                    'drafts' => 0,
                    'archived' => 0,
                    'featured' => 0,
                    'legacy' => 0,
                ],
                'filters' => [
                    'q' => '',
                    'status' => 'all',
                    'category' => 'all',
                    'source' => 'all',
                    'sort' => 'manual',
                ],
                'sortOptions' => self::SORT_OPTIONS,
                'categories' => collect(),
            ]);
        }

        $search = trim((string) $request->string('q'));
        $status = (string) $request->string('status', 'all');
        $category = (string) $request->string('category', 'all');
        $source = (string) $request->string('source', 'all');
        $sort = (string) $request->string('sort', 'manual');

        if (! array_key_exists($sort, self::SORT_OPTIONS)) {
            $sort = 'manual';
        }

        $stats = [
            'total' => GalleryVideo::query()->count(),
            'published' => GalleryVideo::query()->where('status', GalleryVideo::STATUS_PUBLISHED)->count(),
            'drafts' => GalleryVideo::query()->where('status', GalleryVideo::STATUS_DRAFT)->count(),
            'archived' => GalleryVideo::query()->where('status', GalleryVideo::STATUS_ARCHIVED)->count(),
            'featured' => GalleryVideo::query()->where('is_featured', true)->count(),
            'legacy' => GalleryVideo::query()->whereNotNull('legacy_source')->count(),
        ];

        $videosQuery = GalleryVideo::query();

        if ($search !== '') {
            $videosQuery->where(function ($query) use ($search): void {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('category_label', 'like', "%{$search}%")
                    ->orWhere('video_url', 'like', "%{$search}%");
            });
        }

        if (in_array($status, [GalleryVideo::STATUS_PUBLISHED, GalleryVideo::STATUS_DRAFT, GalleryVideo::STATUS_ARCHIVED], true)) {
            $videosQuery->where('status', $status);
        }

        if ($category !== 'all') {
            $videosQuery->where('category_key', $category);
        }

        if ($source === 'legacy') {
            $videosQuery->whereNotNull('legacy_source');
        }

        if ($source === 'manual') {
            $videosQuery->whereNull('legacy_source');
        }

        $this->applySort($videosQuery, $sort);

        $videos = $videosQuery->paginate(12)->withQueryString();
        $categories = GalleryVideo::query()
            ->whereNotNull('category_key')
            ->orderBy('category_label')
            ->get(['category_key', 'category_label'])
            ->unique('category_key')
            ->values();

        return view('pages.admin.gallery-videos.index', [
            'videos' => $videos,
            'autoImportedCount' => $importResult,
            'stats' => $stats,
            'filters' => [
                'q' => $search,
                'status' => $status,
                'category' => $category,
                'source' => in_array($source, ['all', 'legacy', 'manual'], true) ? $source : 'all',
                'sort' => $sort,
            ],
            'sortOptions' => self::SORT_OPTIONS,
            'categories' => $categories,
        ]);
    }

    public function importLegacy(GalleryVideoImportService $galleryVideoImportService): RedirectResponse
    {
        $result = $galleryVideoImportService->import();
        Cache::forget(GalleryVideoImportService::PUBLIC_CACHE_KEY);

        $message = sprintf(
            'Import legacy termine : %d creee(s), %d mise(s) a jour, %d ignoree(s).',
            $result['created'] ?? 0,
            $result['updated'] ?? 0,
            $result['skipped'] ?? 0,
        );

        return back()->with(($result['processed'] ?? 0) > 0 ? 'success' : 'info', $message);
    }

    public function importLegacyIfEmpty(GalleryVideoImportService $galleryVideoImportService): RedirectResponse
    {
        $processed = $galleryVideoImportService->importIfEmpty();
        Cache::forget(GalleryVideoImportService::PUBLIC_CACHE_KEY);

        if ($processed > 0) {
            return back()->with('success', sprintf('Import conditionnel termine : %d video(s) legacy ajoutee(s).', $processed));
        }

        return back()->with('info', 'Import conditionnel ignore : la galerie contient deja des videos.');
    }

    public function store(GalleryVideoUpsertRequest $request): RedirectResponse
    {
        $payload = $this->buildPayload($request->validated(), $request);
        $payload['created_by'] = $request->user()->id;
        $payload['updated_by'] = $request->user()->id;

        GalleryVideo::query()->create($payload);
        Cache::forget(GalleryVideoImportService::PUBLIC_CACHE_KEY);

        return back()->with('success', 'Video galerie creee.');
    }

    public function update(GalleryVideoUpsertRequest $request, int $videoId): RedirectResponse
    {
        $video = GalleryVideo::query()->findOrFail($videoId);
        $payload = $this->buildPayload($request->validated(), $request, $video);
        $payload['updated_by'] = $request->user()->id;

        $video->fill($payload)->save();
        Cache::forget(GalleryVideoImportService::PUBLIC_CACHE_KEY);

        return back()->with('success', 'Video galerie mise a jour.');
    }

    public function removeThumbnail(int $videoId): RedirectResponse
    {
        $video = GalleryVideo::query()->findOrFail($videoId);

        $this->deleteStoredThumbnailIfReplaced($video->thumbnail_url, null);

        $video->fill([
            'thumbnail_url' => null,
            'updated_by' => Auth::id(),
        ])->save();
        Cache::forget(GalleryVideoImportService::PUBLIC_CACHE_KEY);

        return back()->with('success', 'Miniature retiree de la video.');
    }

    public function publish(int $videoId): RedirectResponse
    {
        $video = GalleryVideo::query()->findOrFail($videoId);
        $video->fill([
            'status' => GalleryVideo::STATUS_PUBLISHED,
            'published_at' => $video->published_at ?: now(),
            'updated_by' => Auth::id(),
        ])->save();
        Cache::forget(GalleryVideoImportService::PUBLIC_CACHE_KEY);

        return back()->with('success', 'Video publiee.');
    }

    public function unpublish(int $videoId): RedirectResponse
    {
        $video = GalleryVideo::query()->findOrFail($videoId);
        $video->fill([
            'status' => GalleryVideo::STATUS_DRAFT,
            'published_at' => null,
            'updated_by' => Auth::id(),
        ])->save();
        Cache::forget(GalleryVideoImportService::PUBLIC_CACHE_KEY);

        return back()->with('success', 'Video repassee en brouillon.');
    }

    public function archive(int $videoId): RedirectResponse
    {
        $video = GalleryVideo::query()->findOrFail($videoId);
        $video->fill([
            'status' => GalleryVideo::STATUS_ARCHIVED,
            'updated_by' => Auth::id(),
        ])->save();
        Cache::forget(GalleryVideoImportService::PUBLIC_CACHE_KEY);

        return back()->with('success', 'Video archivee.');
    }

    public function destroy(int $videoId): RedirectResponse
    {
        $video = GalleryVideo::query()->findOrFail($videoId);
        $this->deleteStoredThumbnailIfReplaced($video->thumbnail_url, null);
        $this->deleteStoredPreviewVideoIfReplaced($video->preview_video_url, null);
        $video->delete();
        Cache::forget(GalleryVideoImportService::PUBLIC_CACHE_KEY);

        return back()->with('success', 'Video galerie supprimee.');
    }

    public function reorder(Request $request, int $videoId): RedirectResponse
    {
        $validated = $request->validate([
            'direction' => ['nullable', 'in:up,down,top,bottom'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
        ]);

        GalleryVideo::query()->findOrFail($videoId);

        if (array_key_exists('sort_order', $validated) && $validated['sort_order'] !== null) {
            DB::table('gallery_videos')
                ->where('id', $videoId)
                ->update([
                    'sort_order' => (int) $validated['sort_order'],
                    'updated_by' => $request->user()->id,
                    'updated_at' => now(),
                ]);

            $this->rebalanceOrder();
            Cache::forget(GalleryVideoImportService::PUBLIC_CACHE_KEY);

            return back()->with('success', 'Ordre galerie video mis a jour.');
        }

        $direction = $validated['direction'] ?? 'up';
        $orderedIds = GalleryVideo::query()->ordered()->pluck('id')->values()->all();
        $currentIndex = array_search($videoId, $orderedIds, true);

        if ($currentIndex === false) {
            return back()->with('success', 'Video introuvable dans l ordre courant.');
        }

        $targetIndex = match ($direction) {
            'top' => 0,
            'bottom' => count($orderedIds) - 1,
            'down' => min(count($orderedIds) - 1, $currentIndex + 1),
            default => max(0, $currentIndex - 1),
        };

        if ($targetIndex === $currentIndex) {
            return back()->with('success', 'Ordre deja optimal pour cette video.');
        }

        $movedId = $orderedIds[$currentIndex];
        array_splice($orderedIds, $currentIndex, 1);
        array_splice($orderedIds, $targetIndex, 0, [$movedId]);

        $this->persistOrder($orderedIds, $request->user()->id, $videoId);
        Cache::forget(GalleryVideoImportService::PUBLIC_CACHE_KEY);

        return back()->with('success', 'Ordre galerie video mis a jour.');
    }

    private function applySort($query, string $sort): void
    {
        match ($sort) {
            'recent' => $query->orderByDesc('created_at')->orderByDesc('id'),
            'published' => $query->orderByDesc('published_at')->orderByDesc('id'),
            'title_az' => $query->orderBy('title')->orderByDesc('id'),
            'title_za' => $query->orderByDesc('title')->orderByDesc('id'),
            default => $query->ordered(),
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function buildPayload(array $validated, GalleryVideoUpsertRequest $request, ?GalleryVideo $video = null): array
    {
        $title = trim((string) $validated['title']);
        $videoUrl = trim((string) $validated['video_url']);
        $platform = GalleryVideo::resolvePlatform($validated['platform'] ?? $video?->platform, $videoUrl);
        $categoryKey = trim((string) ($validated['category_key'] ?? $video?->category_key ?? ''));
        $categoryLabel = trim((string) ($validated['category_label'] ?? $video?->category_label ?? ''));

        if ($categoryKey === '' && $categoryLabel !== '') {
            $categoryKey = Str::slug($categoryLabel);
        }

        if ($categoryLabel === '' && $categoryKey !== '') {
            $categoryLabel = Str::headline($categoryKey);
        }

        $status = (string) ($validated['status'] ?? $video?->status ?? GalleryVideo::STATUS_DRAFT);
        $publishedAt = $status === GalleryVideo::STATUS_PUBLISHED
            ? ($validated['published_at'] ?? $video?->published_at ?? now())
            : null;
        $thumbnailUrl = array_key_exists('thumbnail_url', $validated)
            ? ($validated['thumbnail_url'] ?: null)
            : $video?->thumbnail_url;

        if ($request->hasFile('thumbnail_image')) {
            $thumbnailUrl = MediaStorage::store($request->file('thumbnail_image'), 'gallery-videos/thumbnails');
        }

        $this->deleteStoredThumbnailIfReplaced($video?->thumbnail_url, $thumbnailUrl);

        $previewVideoUrl = $validated['preview_video_url'] ?? $video?->preview_video_url;

        if ($request->hasFile('preview_video_file')) {
            $previewVideoUrl = MediaStorage::store($request->file('preview_video_file'), 'gallery-videos/previews');
        }

        $this->deleteStoredPreviewVideoIfReplaced($video?->preview_video_url, $previewVideoUrl);

        $sortOrder = array_key_exists('sort_order', $validated) && $validated['sort_order'] !== null
            ? (int) $validated['sort_order']
            : ($video?->sort_order ?? ((int) GalleryVideo::query()->max('sort_order') + 1));

        $isFeatured = array_key_exists('is_featured', $validated) && $validated['is_featured'] !== null
            ? (bool) $validated['is_featured']
            : (bool) ($video?->is_featured ?? false);

        $description = $validated['description'] ?? null;

        return [
            'title' => $title,
            'slug' => GalleryVideo::uniqueSlug((string) ($validated['slug'] ?: $title), $video?->id),
            'excerpt' => null,
            'description' => $description,
            'platform' => $platform,
            'video_url' => $videoUrl,
            'embed_url' => ($validated['embed_url'] ?? $video?->embed_url) ?: GalleryVideo::buildEmbedUrl($videoUrl, $platform),
            'thumbnail_url' => $thumbnailUrl,
            'preview_video_url' => $previewVideoUrl,
            'preview_video_webm_url' => $validated['preview_video_webm_url'] ?? $video?->preview_video_webm_url,
            'category_key' => $categoryKey !== '' ? $categoryKey : null,
            'category_label' => $categoryLabel !== '' ? $categoryLabel : null,
            'status' => $status,
            'sort_order' => $sortOrder,
            'is_featured' => $isFeatured,
            'published_at' => $publishedAt,
            'legacy_source' => $video?->legacy_source,
            'imported_hash' => $video?->imported_hash,
        ];
    }

    private function deleteStoredThumbnailIfReplaced(?string $currentThumbnailUrl, ?string $nextThumbnailUrl): void
    {
        $storagePath = $this->managedGalleryVideoPath($currentThumbnailUrl, 'gallery-videos/thumbnails/');

        if ($storagePath === null) {
            return;
        }

        if ($currentThumbnailUrl === $nextThumbnailUrl) {
            return;
        }

        MediaStorage::delete($storagePath);
    }

    private function deleteStoredPreviewVideoIfReplaced(?string $currentPreviewVideoUrl, ?string $nextPreviewVideoUrl): void
    {
        $storagePath = $this->managedGalleryVideoPath($currentPreviewVideoUrl, 'gallery-videos/previews/');

        if ($storagePath === null) {
            return;
        }

        if ($currentPreviewVideoUrl === $nextPreviewVideoUrl) {
            return;
        }

        MediaStorage::delete($storagePath);
    }

    private function managedGalleryVideoPath(?string $value, string $expectedPrefix): ?string
    {
        if (! filled($value)) {
            return null;
        }

        $stringValue = (string) $value;

        if (Str::startsWith($stringValue, '/storage/'.$expectedPrefix)) {
            return Str::after($stringValue, '/storage/');
        }

        if (Str::startsWith($stringValue, $expectedPrefix)) {
            return $stringValue;
        }

        return null;
    }

    private function rebalanceOrder(): void
    {
        $orderedIds = GalleryVideo::query()->orderBy('sort_order')->orderByDesc('id')->pluck('id')->values()->all();
        $this->persistOrder($orderedIds, Auth::id());
    }

    /**
     * @param  list<int>  $orderedIds
     */
    private function persistOrder(array $orderedIds, ?int $userId, ?int $promotedVideoId = null): void
    {
        foreach ($orderedIds as $index => $id) {
            DB::table('gallery_videos')->where('id', $id)->update([
                'sort_order' => $index,
                'is_featured' => $promotedVideoId !== null ? $id === $promotedVideoId : DB::raw('is_featured'),
                'updated_by' => $userId,
                'updated_at' => now(),
            ]);
        }
    }
}