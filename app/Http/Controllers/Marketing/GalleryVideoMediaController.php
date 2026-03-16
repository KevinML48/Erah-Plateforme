<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Support\MediaStorage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GalleryVideoMediaController extends Controller
{
    public function thumbnail(string $path): StreamedResponse
    {
        return $this->respondWithMedia($path, 'gallery-videos/thumbnails/');
    }

    public function preview(string $path): StreamedResponse
    {
        return $this->respondWithMedia($path, 'gallery-videos/previews/');
    }

    private function respondWithMedia(string $path, string $expectedPrefix): StreamedResponse
    {
        $normalizedPath = trim($path, '/');
        $disk = MediaStorage::resolveDiskForPath($normalizedPath, (string) config('filesystems.media_disk', MediaStorage::publicDisk()));

        if (! Str::startsWith($normalizedPath, $expectedPrefix)) {
            abort(404);
        }

        if (! Storage::disk($disk)->exists($normalizedPath)) {
            abort(404);
        }

        return Storage::disk($disk)->response($normalizedPath, null, [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}