<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Support\MediaStorage;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicMediaController extends Controller
{
    public function show(string $path): StreamedResponse
    {
        $normalizedPath = trim($path, '/');

        if ($normalizedPath === '' || Str::contains($normalizedPath, ['../', '..\\'])) {
            abort(404);
        }

        if (! Storage::disk(MediaStorage::publicDisk())->exists($normalizedPath)) {
            abort(404);
        }

        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk(MediaStorage::publicDisk());

        return $storage->response($normalizedPath, null, [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}