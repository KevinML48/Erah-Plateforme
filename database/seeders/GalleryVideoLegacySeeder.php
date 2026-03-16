<?php

namespace Database\Seeders;

use App\Models\GalleryVideo;
use App\Services\GalleryVideoImportService;
use Illuminate\Database\Seeder;

class GalleryVideoLegacySeeder extends Seeder
{
    /**
     * Seed the application's gallery videos from the legacy template source.
     */
    public function run(): void
    {
        /** @var GalleryVideoImportService $importService */
        $importService = app(GalleryVideoImportService::class);

        $result = $importService->import();

        if (($result['processed'] ?? 0) === 0) {
            $result = $this->seedFallbackVideos();
        }

        $this->command?->info(sprintf(
            'Gallery videos legacy import: %d created, %d updated, %d skipped.',
            $result['created'] ?? 0,
            $result['updated'] ?? 0,
            $result['skipped'] ?? 0,
        ));
    }

    /**
     * @return array{processed:int,created:int,updated:int,skipped:int,found:int}
     */
    private function seedFallbackVideos(): array
    {
        $created = 0;
        $updated = 0;

        /** @var GalleryVideoImportService $importService */
        $importService = app(GalleryVideoImportService::class);
        $items = $importService->fallbackItems();

        foreach ($items as $item) {
            $video = GalleryVideo::query()->where('imported_hash', $item['imported_hash'])->first();

            if ($video && $video->updated_by !== null) {
                continue;
            }

            if ($video) {
                $video->fill($item)->save();
                $updated++;

                continue;
            }

            GalleryVideo::query()->create(array_merge($item, [
                'slug' => GalleryVideo::uniqueSlug($item['title']),
            ]));
            $created++;
        }

        return [
            'processed' => $created + $updated,
            'created' => $created,
            'updated' => $updated,
            'skipped' => 0,
            'found' => count($items),
        ];
    }
}