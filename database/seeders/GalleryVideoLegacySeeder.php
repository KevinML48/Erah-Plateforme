<?php

namespace Database\Seeders;

use App\Models\GalleryVideo;
use App\Services\GalleryVideoImportService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GalleryVideoLegacySeeder extends Seeder
{
    private const FALLBACK_SOURCE = '_template_site/galerie-video.html';

    /**
     * Seed the application's gallery videos from the legacy template source.
     */
    public function run(): void
    {
        /** @var GalleryVideoImportService $importService */
        $importService = app(GalleryVideoImportService::class);

        $result = $importService->import();

        if (($result['processed'] ?? 0) === 0 && GalleryVideo::query()->count() === 0) {
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

        foreach ($this->fallbackVideos() as $index => $item) {
            $video = GalleryVideo::query()->where('imported_hash', $item['imported_hash'])->first();

            if ($video && $video->updated_by !== null) {
                continue;
            }

            if ($video) {
                $video->fill($item)->save();
                $updated++;

                continue;
            }

            GalleryVideo::query()->create($item + [
                'slug' => GalleryVideo::uniqueSlug($item['title']),
            ]);
            $created++;
        }

        return [
            'processed' => $created + $updated,
            'created' => $created,
            'updated' => $updated,
            'skipped' => 0,
            'found' => count($this->fallbackVideos()),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fallbackVideos(): array
    {
        $videos = [
            [
                'title' => 'Présentation',
                'category_label' => 'Club',
                'video_url' => 'https://youtu.be/MCh7wI7gMOU',
                'preview_video_url' => '/template/assets/vids/Presentation ERAH.mp4',
                'preview_video_webm_url' => '/template/assets/vids/Presentation ERAH.webm',
            ],
            [
                'title' => 'VCL Split 1 2026',
                'category_label' => 'Valorant',
                'video_url' => 'https://youtu.be/gfKDIPuq2JY',
                'preview_video_url' => '/template/assets/vids/merci-VCL - Trim.mp4',
                'preview_video_webm_url' => '/template/assets/vids/merci-VCL - Trim.webm',
            ],
            [
                'title' => 'Interview',
                'category_label' => 'LAN',
                'video_url' => 'https://youtu.be/n_LEo-tp3Jk',
                'preview_video_url' => '/template/assets/vids/interview-GA-2025.mp4',
                'preview_video_webm_url' => '/template/assets/vids/interview-GA-2025.webm',
            ],
            [
                'title' => 'HopLan 2025',
                'category_label' => 'LAN',
                'video_url' => 'https://youtu.be/6-ebq2tKpAs',
                'preview_video_url' => '/template/assets/vids/interview-HopLan-2025.mp4',
                'preview_video_webm_url' => '/template/assets/vids/interview-HopLan-2025.webm',
            ],
            [
                'title' => 'InfinityUP',
                'category_label' => 'Event',
                'video_url' => 'https://youtu.be/7IxK35ld2Pg',
                'preview_video_url' => '/template/assets/vids/Video InfinityUP presentation.mp4',
                'preview_video_webm_url' => '/template/assets/vids/Video InfinityUP presentation.webm',
            ],
            [
                'title' => 'HopLan 2024',
                'category_label' => 'LAN',
                'video_url' => 'https://youtu.be/I1o44CVvCFA',
                'preview_video_url' => '/template/assets/vids/interview-HopLan-2024.mp4',
                'preview_video_webm_url' => '/template/assets/vids/interview-HopLan-2024.webm',
            ],
            [
                'title' => 'Bootcamp GC',
                'category_label' => 'Esport',
                'video_url' => 'https://youtu.be/80qtJPHgmqY',
                'preview_video_url' => '/template/assets/vids/bootcamp feminin.mp4',
                'preview_video_webm_url' => '/template/assets/vids/bootcamp feminin.webm',
            ],
            [
                'title' => 'Recap MW3',
                'category_label' => 'Esport',
                'video_url' => 'https://youtu.be/KTYwsLZNBqA',
                'preview_video_url' => '/template/assets/vids/recap mw3 - Trim.mp4',
                'preview_video_webm_url' => '/template/assets/vids/recap mw3 - Trim.webm',
            ],
        ];

        return array_map(function (array $video, int $index): array {
            $platform = GalleryVideo::resolvePlatform(null, $video['video_url']);

            return [
                'title' => $video['title'],
                'slug' => GalleryVideo::uniqueSlug($video['title']),
                'excerpt' => null,
                'description' => null,
                'platform' => $platform,
                'video_url' => $video['video_url'],
                'embed_url' => GalleryVideo::buildEmbedUrl($video['video_url'], $platform),
                'thumbnail_url' => null,
                'preview_video_url' => $video['preview_video_url'],
                'preview_video_webm_url' => $video['preview_video_webm_url'],
                'category_key' => Str::slug($video['category_label']),
                'category_label' => $video['category_label'],
                'status' => GalleryVideo::STATUS_PUBLISHED,
                'sort_order' => $index,
                'is_featured' => $index === 0,
                'published_at' => now(),
                'legacy_source' => self::FALLBACK_SOURCE,
                'imported_hash' => sha1(implode('|', [
                    self::FALLBACK_SOURCE,
                    $video['title'],
                    $video['video_url'],
                    $video['category_label'],
                    $index,
                ])),
                'created_by' => null,
                'updated_by' => null,
            ];
        }, $videos, array_keys($videos));
    }
}