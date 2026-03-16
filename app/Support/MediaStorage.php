<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaStorage
{
    public static function disk(): string
    {
        return (string) config('filesystems.media_disk', 'public');
    }

    public static function store(UploadedFile $file, string $directory): string
    {
        return $file->store($directory, static::disk());
    }

    public static function url(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        $value = (string) $path;

        if (Str::startsWith($value, ['http://', 'https://', '/'])) {
            return $value;
        }

        return Storage::disk(static::disk())->url($value);
    }

    public static function delete(?string $path): void
    {
        if (! static::isManagedPath($path)) {
            return;
        }

        Storage::disk(static::disk())->delete((string) $path);
    }

    public static function isManagedPath(?string $path): bool
    {
        return filled($path)
            && ! Str::startsWith((string) $path, ['http://', 'https://', '/']);
    }
}
