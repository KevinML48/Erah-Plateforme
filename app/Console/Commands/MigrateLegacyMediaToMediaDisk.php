<?php

namespace App\Console\Commands;

use App\Models\GalleryPhoto;
use App\Models\GalleryVideo;
use App\Models\User;
use App\Support\MediaStorage;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class MigrateLegacyMediaToMediaDisk extends Command
{
    protected $signature = 'media:migrate-legacy-public-to-media-disk
        {--disk= : Target disk override. Defaults to filesystems.media_disk}
        {--chunk=100 : Chunk size for DB scanning}
        {--dry-run : Show what would be migrated without writing files or DB updates}';

    protected $description = 'Copy legacy managed media from the public disk to the configured media disk and update records.';

    public function handle(): int
    {
        $targetDisk = MediaStorage::disk($this->option('disk') ?: null);
        $sourceDisk = MediaStorage::publicDisk();
        $dryRun = (bool) $this->option('dry-run');
        $chunkSize = max(1, (int) $this->option('chunk'));

        if ($targetDisk === $sourceDisk) {
            $this->warn('Le disque cible est deja "public". Aucune migration necessaire.');

            return self::SUCCESS;
        }

        $stats = [
            'scanned' => 0,
            'migrated' => 0,
            'already_on_target' => 0,
            'missing_source' => 0,
            'skipped' => 0,
            'failed' => 0,
        ];

        $this->info(sprintf(
            'Migration des medias legacy: %s -> %s%s',
            $sourceDisk,
            $targetDisk,
            $dryRun ? ' (dry-run)' : ''
        ));

        User::query()
            ->whereNotNull('avatar_path')
            ->orderBy('id')
            ->chunkById($chunkSize, function (Collection $users) use ($dryRun, $sourceDisk, $stats, $targetDisk): void {
                foreach ($users as $user) {
                    $this->migrateField($user, 'avatar_path', null, $sourceDisk, $targetDisk, $dryRun, $stats);
                }
            });

        GalleryPhoto::query()
            ->where(function ($query): void {
                $query->whereNotNull('image_path')
                    ->orWhereNotNull('video_path');
            })
            ->orderBy('id')
            ->chunkById($chunkSize, function (Collection $photos) use ($dryRun, $sourceDisk, $stats, $targetDisk): void {
                foreach ($photos as $photo) {
                    $this->migrateField($photo, 'image_path', 'storage_disk', $sourceDisk, $targetDisk, $dryRun, $stats);
                    $this->migrateField($photo, 'video_path', 'storage_disk', $sourceDisk, $targetDisk, $dryRun, $stats);
                }
            });

        GalleryVideo::query()
            ->where(function ($query): void {
                $query->whereNotNull('thumbnail_url')
                    ->orWhereNotNull('preview_video_url');
            })
            ->orderBy('id')
            ->chunkById($chunkSize, function (Collection $videos) use ($dryRun, $sourceDisk, $stats, $targetDisk): void {
                foreach ($videos as $video) {
                    $this->migrateField($video, 'thumbnail_url', null, $sourceDisk, $targetDisk, $dryRun, $stats);
                    $this->migrateField($video, 'preview_video_url', null, $sourceDisk, $targetDisk, $dryRun, $stats);
                }
            });

        $this->newLine();
        $this->table(
            ['scanned', 'migrated', 'already_on_target', 'missing_source', 'skipped', 'failed'],
            [[
                $stats['scanned'],
                $stats['migrated'],
                $stats['already_on_target'],
                $stats['missing_source'],
                $stats['skipped'],
                $stats['failed'],
            ]]
        );

        return $stats['failed'] > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @param  array<string, int>  $stats
     */
    private function migrateField(
        Model $model,
        string $field,
        ?string $diskField,
        string $sourceDisk,
        string $targetDisk,
        bool $dryRun,
        array &$stats,
    ): void {
        $value = $model->getAttribute($field);

        if (! is_string($value) || $value === '') {
            return;
        }

        $stats['scanned']++;

        $path = $this->normalizeManagedPath($value);

        if ($path === null) {
            $stats['skipped']++;
            return;
        }

        if ($diskField !== null && $model->getAttribute($diskField) === $targetDisk) {
            $stats['already_on_target']++;
            return;
        }

        if (Storage::disk($targetDisk)->exists($path)) {
            $this->persistModelUpdate($model, $diskField, $targetDisk, $dryRun);
            $stats['already_on_target']++;
            return;
        }

        if (! Storage::disk($sourceDisk)->exists($path)) {
            $stats['missing_source']++;
            $this->line(sprintf('Source absente: [%s:%s#%s] %s', class_basename($model), $field, $model->getKey(), $path));
            return;
        }

        if ($dryRun) {
            $this->line(sprintf('Dry-run migration: [%s:%s#%s] %s', class_basename($model), $field, $model->getKey(), $path));
            $stats['migrated']++;
            return;
        }

        try {
            $stream = Storage::disk($sourceDisk)->readStream($path);

            if (! is_resource($stream)) {
                $stats['failed']++;
                $this->error(sprintf('Lecture impossible: [%s:%s#%s] %s', class_basename($model), $field, $model->getKey(), $path));
                return;
            }

            $copied = Storage::disk($targetDisk)->writeStream($path, $stream, ['visibility' => 'public']);

            if (is_resource($stream)) {
                fclose($stream);
            }

            if (! $copied) {
                $stats['failed']++;
                $this->error(sprintf('Copie impossible: [%s:%s#%s] %s', class_basename($model), $field, $model->getKey(), $path));
                return;
            }

            $this->persistModelUpdate($model, $diskField, $targetDisk, false);
            $stats['migrated']++;
        } catch (Throwable $exception) {
            $stats['failed']++;
            $this->error(sprintf(
                'Erreur migration [%s:%s#%s] %s: %s',
                class_basename($model),
                $field,
                $model->getKey(),
                $path,
                $exception->getMessage()
            ));
        }
    }

    private function normalizeManagedPath(string $value): ?string
    {
        $path = trim($value);

        if ($path === '' || Str::startsWith($path, ['http://', 'https://'])) {
            return null;
        }

        if (Str::startsWith($path, '/storage/')) {
            $path = Str::after($path, '/storage/');
        }

        if (Str::startsWith($path, '/')) {
            return null;
        }

        return MediaStorage::isManagedPath($path) ? $path : null;
    }

    private function persistModelUpdate(Model $model, ?string $diskField, string $targetDisk, bool $dryRun): void
    {
        if ($dryRun || $diskField === null) {
            return;
        }

        if ($model->getAttribute($diskField) === $targetDisk) {
            return;
        }

        $model->forceFill([$diskField => $targetDisk])->save();
    }
}