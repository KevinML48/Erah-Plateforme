<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryVideoMediaController extends Controller
{
    public function thumbnail(string $path): Response
    {
        return $this->respondWithMedia($path, 'gallery-videos/thumbnails/');
    }

    public function preview(string $path): Response
    {
        return $this->respondWithMedia($path, 'gallery-videos/previews/');
    }

    private function respondWithMedia(string $path, string $expectedPrefix): Response
    {
        $normalizedPath = trim($path, '/');
        $disk = (string) config('filesystems.media_disk', 'public');

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