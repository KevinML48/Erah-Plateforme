<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class MediaStorage
{
    private const PUBLIC_DISK = 'public';

    public static function disk(?string $disk = null): string
    {
        $resolvedDisk = $disk ?: config('filesystems.media_disk', self::PUBLIC_DISK);

        return filled($resolvedDisk) ? (string) $resolvedDisk : self::PUBLIC_DISK;
    }

    public static function publicDisk(): string
    {
        return self::PUBLIC_DISK;
    }

    public static function store(UploadedFile $file, string $directory): string
    {
        return $file->storePublicly($directory, ['disk' => static::disk()]);
    }

    public static function url(?string $path, ?string $disk = null): ?string
    {
        if (! filled($path)) {
            return null;
        }

        $value = (string) $path;

        if (Str::startsWith($value, ['http://', 'https://', '/'])) {
            return $value;
        }

        return static::diskUrl($value, static::resolveDiskForPath($value, $disk));
    }

    public static function diskUrl(string $path, ?string $disk = null): ?string
    {
        $resolvedDisk = static::disk($disk);

        if ($resolvedDisk === static::publicDisk()) {
            return route('media.public.file', ['path' => ltrim($path, '/')]);
        }

        try {
            /** @var FilesystemAdapter $storage */
            $storage = Storage::disk($resolvedDisk);

            return $storage->url($path);
        } catch (Throwable) {
            return null;
        }
    }

    public static function resolveDiskForPath(string $path, ?string $preferredDisk = null): string
    {
        $resolvedDisk = static::disk($preferredDisk);

        // Keep old public-media records readable after switching MEDIA_DISK to s3.
        if ($resolvedDisk !== static::publicDisk() && static::pathExists($path, static::publicDisk())) {
            return static::publicDisk();
        }

        return $resolvedDisk;
    }

    public static function delete(?string $path, ?string $disk = null): void
    {
        if (! static::isManagedPath($path)) {
            return;
        }

        $managedPath = (string) $path;
        $candidateDisks = array_values(array_unique([
            static::disk($disk),
            static::publicDisk(),
        ]));

        foreach ($candidateDisks as $resolvedDisk) {
            try {
                Storage::disk($resolvedDisk)->delete($managedPath);
            } catch (Throwable) {
                // Ignore adapter-level delete failures on disks that do not own the file.
            }
        }
    }

    public static function isManagedPath(?string $path): bool
    {
        return filled($path)
            && ! Str::startsWith((string) $path, ['http://', 'https://', '/']);
    }

    public static function pathExists(?string $path, ?string $disk = null): bool
    {
        if (! static::isManagedPath($path)) {
            return false;
        }

        try {
            return Storage::disk(static::disk($disk))->exists((string) $path);
        } catch (Throwable) {
            return false;
        }
    }

    public static function fallbackAvatarUrl(): string
    {
        return asset('template/assets/img/logo.png');
    }
}
