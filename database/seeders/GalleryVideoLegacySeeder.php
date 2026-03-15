<?php

namespace Database\Seeders;

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

        $this->command?->info(sprintf(
            'Gallery videos legacy import: %d created, %d updated, %d skipped.',
            $result['created'] ?? 0,
            $result['updated'] ?? 0,
            $result['skipped'] ?? 0,
        ));
    }
}