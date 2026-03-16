<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\GalleryPhotoUpsertRequest;
use App\Models\GalleryPhoto;
use App\Services\GalleryPhotoImportService;
use App\Support\MediaStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GalleryPhotoAdminController extends Controller
{
    private const SORT_OPTIONS = [
        'manual' => 'Ordre manuel',
        'recent' => 'Plus recents',
        'oldest' => 'Plus anciens',
        'title_az' => 'Titre A-Z',
        'title_za' => 'Titre Z-A',
    ];

    public function index(Request $request, GalleryPhotoImportService $galleryPhotoImportService): View
    {
        $importResult = $galleryPhotoImportService->importIfEmpty();

        if (! Schema::hasTable('gallery_photos')) {
            return view('pages.admin.gallery-photos.index', [
                'photos' => new LengthAwarePaginator([], 0, 18),
                'autoImportedCount' => $importResult,
                'stats' => [
                    'total' => 0,
                    'active' => 0,
                    'inactive' => 0,
                    'images' => 0,
                    'videos' => 0,
                    'scheduled' => 0,
                ],
                'filters' => [
                    'q' => '',
                    'status' => 'all',
                    'type' => 'all',
                    'sort' => 'manual',
                ],
                'sortOptions' => self::SORT_OPTIONS,
            ]);
        }

        $total = GalleryPhoto::query()->count();
        $active = GalleryPhoto::query()->active()->count();

        $stats = [
            'total' => $total,
            'active' => $active,
            'inactive' => max(0, $total - $active),
            'images' => GalleryPhoto::query()->where('media_type', GalleryPhoto::MEDIA_TYPE_IMAGE)->count(),
            'videos' => GalleryPhoto::query()->where('media_type', GalleryPhoto::MEDIA_TYPE_VIDEO)->count(),
            'scheduled' => GalleryPhoto::query()
                ->whereNotNull('published_at')
                ->where('published_at', '>', now())
                ->count(),
        ];

        $search = trim((string) $request->string('q'));
        $status = (string) $request->string('status', 'all');
        $type = (string) $request->string('type', 'all');
        $sort = (string) $request->string('sort', 'manual');

        if (! array_key_exists($sort, self::SORT_OPTIONS)) {
            $sort = 'manual';
        }

        $photosQuery = GalleryPhoto::query();

        if ($search !== '') {
            $photosQuery->where(function ($query) use ($search): void {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('filter_label', 'like', "%{$search}%")
                    ->orWhere('category_label', 'like', "%{$search}%");
            });
        }

        if ($status === 'active') {
            $photosQuery->where('is_active', true);
        } elseif ($status === 'inactive') {
            $photosQuery->where('is_active', false);
        } elseif ($status === 'scheduled') {
            $photosQuery->whereNotNull('published_at')->where('published_at', '>', now());
        }

        if (in_array($type, [GalleryPhoto::MEDIA_TYPE_IMAGE, GalleryPhoto::MEDIA_TYPE_VIDEO], true)) {
            $photosQuery->where('media_type', $type);
        }

        $this->applySort($photosQuery, $sort);

        $photos = $photosQuery->paginate(18)->withQueryString();

        return view('pages.admin.gallery-photos.index', [
            'photos' => $photos,
            'autoImportedCount' => $importResult,
            'stats' => $stats,
            'filters' => [
                'q' => $search,
                'status' => $status,
                'type' => $type,
                'sort' => $sort,
            ],
            'sortOptions' => self::SORT_OPTIONS,
        ]);
    }

    public function store(GalleryPhotoUpsertRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $media = $this->storeMediaFile($request->file('media_file'));

        GalleryPhoto::query()->create([
            'title' => $validated['title'] ?: $this->fallbackTitle($request->file('media_file')),
            'description' => $validated['description'] ?? null,
            'image_path' => $media['image_path'],
            'video_path' => $media['video_path'],
            'media_type' => $media['media_type'],
            'alt_text' => $validated['alt_text'] ?? null,
            'filter_key' => $validated['filter_key'] ?: null,
            'filter_label' => $this->normalizeLabel($validated['filter_label'] ?? null, $validated['filter_key'] ?? null),
            'category_label' => $validated['category_label'] ?? null,
            'cursor_label' => $validated['cursor_label'] ?? null,
            'sort_order' => (int) $validated['sort_order'],
            'is_active' => $request->boolean('is_active', true),
            'published_at' => $validated['published_at'] ?? null,
            'storage_disk' => $media['storage_disk'],
            'media_mime_type' => $media['media_mime_type'],
            'media_size' => $media['media_size'],
            'legacy_source' => null,
            'imported_hash' => null,
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Photo galerie creee.');
    }

    public function update(GalleryPhotoUpsertRequest $request, int $photoId): RedirectResponse
    {
        $photo = GalleryPhoto::query()->findOrFail($photoId);
        $validated = $request->validated();
        $oldManagedMedia = $this->managedMediaReference($photo);
        $media = $request->hasFile('media_file') ? $this->storeMediaFile($request->file('media_file')) : null;

        $photo->fill([
            'title' => $validated['title'] ?: $photo->title ?: $this->fallbackTitle($request->file('media_file')),
            'description' => $validated['description'] ?? null,
            'image_path' => $media['image_path'] ?? $photo->image_path,
            'video_path' => $media['video_path'] ?? $photo->video_path,
            'media_type' => $media['media_type'] ?? $photo->media_type,
            'alt_text' => $validated['alt_text'] ?? null,
            'filter_key' => $validated['filter_key'] ?: null,
            'filter_label' => $this->normalizeLabel($validated['filter_label'] ?? null, $validated['filter_key'] ?? null),
            'category_label' => $validated['category_label'] ?? null,
            'cursor_label' => $validated['cursor_label'] ?? null,
            'sort_order' => (int) $validated['sort_order'],
            'is_active' => $request->boolean('is_active', false),
            'published_at' => $validated['published_at'] ?? null,
            'storage_disk' => $media['storage_disk'] ?? $photo->storage_disk,
            'media_mime_type' => $media['media_mime_type'] ?? $photo->media_mime_type,
            'media_size' => $media['media_size'] ?? $photo->media_size,
            'legacy_source' => $photo->legacy_source,
            'updated_by' => $request->user()->id,
        ])->save();

        if ($media !== null) {
            $this->deleteManagedMediaIfUnused($oldManagedMedia, $photo->id);
        }

        return back()->with('success', 'Photo galerie mise a jour.');
    }

    public function destroy(int $photoId): RedirectResponse
    {
        $photo = GalleryPhoto::query()->findOrFail($photoId);
        $managedMedia = $this->managedMediaReference($photo);

        $photo->delete();
        $this->deleteManagedMediaIfUnused($managedMedia);

        return back()->with('success', 'Photo galerie supprimee.');
    }

    public function toggle(int $photoId): RedirectResponse
    {
        $photo = GalleryPhoto::query()->findOrFail($photoId);
        $photo->is_active = ! $photo->is_active;
        $photo->updated_by = auth()->id();
        $photo->save();

        return back()->with('success', $photo->is_active ? 'Photo activee.' : 'Photo desactivee.');
    }

    public function reorder(Request $request, int $photoId): RedirectResponse
    {
        $validated = $request->validate([
            'direction' => ['nullable', 'in:up,down,top,bottom'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
        ]);

        GalleryPhoto::query()->findOrFail($photoId);

        if (array_key_exists('sort_order', $validated) && $validated['sort_order'] !== null) {
            DB::table('gallery_photos')
                ->where('id', $photoId)
                ->update([
                    'sort_order' => (int) $validated['sort_order'],
                    'updated_by' => $request->user()->id,
                    'updated_at' => now(),
                ]);

            $this->rebalanceOrder();

            return back()->with('success', 'Ordre galerie mis a jour.');
        }

        $direction = $validated['direction'] ?? 'up';
        $orderedIds = GalleryPhoto::query()->ordered()->pluck('id')->values()->all();
        $currentIndex = array_search($photoId, $orderedIds, true);

        if ($currentIndex === false) {
            return back()->with('success', 'Media galerie introuvable dans l ordre courant.');
        }

        $targetIndex = match ($direction) {
            'top' => 0,
            'bottom' => count($orderedIds) - 1,
            'down' => min(count($orderedIds) - 1, $currentIndex + 1),
            default => max(0, $currentIndex - 1),
        };

        if ($targetIndex === $currentIndex) {
            return back()->with('success', 'Ordre galerie deja optimal pour ce media.');
        }

        $movedId = $orderedIds[$currentIndex];
        array_splice($orderedIds, $currentIndex, 1);
        array_splice($orderedIds, $targetIndex, 0, [$movedId]);

        $this->persistOrder($orderedIds, $request->user()->id, $photoId);

        return back()->with('success', 'Ordre galerie mis a jour.');
    }

    /**
     * @return array{image_path:?string,video_path:?string,media_type:string,storage_disk:string,media_mime_type:?string,media_size:?int}
     */
    private function storeMediaFile(?UploadedFile $file): array
    {
        if (! $file) {
            return [
                'image_path' => null,
                'video_path' => null,
                'media_type' => GalleryPhoto::MEDIA_TYPE_IMAGE,
                'storage_disk' => MediaStorage::disk(),
                'media_mime_type' => null,
                'media_size' => null,
            ];
        }

        $mimeType = $file->getMimeType();
        $isVideo = Str::startsWith((string) $mimeType, 'video/');
        $directory = $isVideo ? 'gallery/videos' : 'gallery/photos';
        $disk = MediaStorage::disk();
        $path = $file->store($directory, $disk);

        return [
            'image_path' => $isVideo ? null : $path,
            'video_path' => $isVideo ? $path : null,
            'media_type' => $isVideo ? GalleryPhoto::MEDIA_TYPE_VIDEO : GalleryPhoto::MEDIA_TYPE_IMAGE,
            'storage_disk' => $disk,
            'media_mime_type' => $mimeType,
            'media_size' => $file->getSize(),
        ];
    }

    /**
     * @return array{disk:string,path:string}|null
     */
    private function managedMediaReference(GalleryPhoto $photo): ?array
    {
        if (blank($photo->storage_disk)) {
            return null;
        }

        $path = $photo->image_path ?: $photo->video_path;
        if (blank($path)) {
            return null;
        }

        return [
            'disk' => (string) $photo->storage_disk,
            'path' => (string) $path,
        ];
    }

    private function deleteManagedMediaIfUnused(?array $media, ?int $ignorePhotoId = null): void
    {
        if ($media === null) {
            return;
        }

        $isStillReferenced = GalleryPhoto::query()
            ->when($ignorePhotoId !== null, fn ($query) => $query->where('id', '!=', $ignorePhotoId))
            ->where('storage_disk', $media['disk'])
            ->where(function ($query) use ($media): void {
                $query->where('image_path', $media['path'])
                    ->orWhere('video_path', $media['path']);
            })
            ->exists();

        if (! $isStillReferenced) {
            Storage::disk($media['disk'])->delete($media['path']);
        }
    }

    private function fallbackTitle(?UploadedFile $file): ?string
    {
        if (! $file) {
            return null;
        }

        return Str::headline(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
    }

    private function normalizeLabel(?string $label, ?string $filterKey): ?string
    {
        if (filled($label)) {
            return $label;
        }

        if (filled($filterKey)) {
            return Str::headline((string) $filterKey);
        }

        return null;
    }

    private function applySort($query, string $sort): void
    {
        match ($sort) {
            'recent' => $query->orderByDesc('created_at')->orderByDesc('id'),
            'oldest' => $query->orderBy('created_at')->orderBy('id'),
            'title_az' => $query->orderByRaw("case when title is null or title = '' then 1 else 0 end")
                ->orderBy('title')
                ->orderBy('id'),
            'title_za' => $query->orderByRaw("case when title is null or title = '' then 1 else 0 end")
                ->orderByDesc('title')
                ->orderByDesc('id'),
            default => $query->ordered(),
        };
    }

    private function rebalanceOrder(): void
    {
        $orderedIds = GalleryPhoto::query()->ordered()->pluck('id')->values()->all();
        $this->persistOrder($orderedIds);
    }

    /**
     * @param  array<int, int>  $orderedIds
     */
    private function persistOrder(array $orderedIds, ?int $updatedBy = null, ?int $movedPhotoId = null): void
    {
        if ($orderedIds === []) {
            return;
        }

        DB::transaction(function () use ($orderedIds, $updatedBy, $movedPhotoId): void {
            foreach ($orderedIds as $index => $id) {
                $payload = ['sort_order' => $index];

                if ($updatedBy !== null && $movedPhotoId !== null && $id === $movedPhotoId) {
                    $payload['updated_by'] = $updatedBy;
                    $payload['updated_at'] = now();
                }

                DB::table('gallery_photos')->where('id', $id)->update($payload);
            }
        });
    }
}
