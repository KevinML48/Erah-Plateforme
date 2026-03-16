<?php

namespace App\Services;

use App\Models\GalleryVideo;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GalleryVideoImportService
{
    private const LEGACY_SOURCE = '_template_site/galerie-video.html';
    public const PUBLIC_CACHE_KEY = 'marketing.gallery-videos.payload';

    public function legacySourceExists(): bool
    {
        return file_exists(base_path(self::LEGACY_SOURCE));
    }

    public function importIfEmpty(): int
    {
        if (! Schema::hasTable('gallery_videos')) {
            return 0;
        }

        if (GalleryVideo::query()->exists()) {
            return 0;
        }

        return $this->import()['processed'];
    }

    public function import(): array
    {
        if (! Schema::hasTable('gallery_videos')) {
            return ['processed' => 0, 'created' => 0, 'updated' => 0, 'skipped' => 0, 'found' => 0];
        }

        $items = $this->extractLegacyItems();
        if ($items === []) {
            $items = $this->fallbackItems();
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($items as $item) {
            $existing = GalleryVideo::query()->where('imported_hash', $item['imported_hash'])->first();

            if ($existing && $existing->updated_by !== null) {
                $skipped++;

                continue;
            }

            $item['slug'] = $existing?->slug ?: GalleryVideo::uniqueSlug((string) $item['title']);

            if ($existing) {
                $existing->fill($item)->save();
                $updated++;

                continue;
            }

            GalleryVideo::query()->create($item);
            $created++;
        }

        return [
            'processed' => $created + $updated,
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'found' => count($items),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function extractLegacyItems(): array
    {
        if (! $this->legacySourceExists()) {
            return [];
        }

        $source = file_get_contents(base_path(self::LEGACY_SOURCE));
        if ($source === false) {
            return [];
        }

        $document = new DOMDocument('1.0', 'UTF-8');

        libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="utf-8" ?>'.$source, LIBXML_NOWARNING | LIBXML_NOERROR);
        libxml_clear_errors();

        $xpath = new DOMXPath($document);
        $nodes = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' swiper-slide ')]");

        if ($nodes === false) {
            return [];
        }

        $items = [];
        $sortOrder = 0;

        foreach ($nodes as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $title = $this->textContent($xpath, ".//*[contains(concat(' ', normalize-space(@class), ' '), ' tt-posl-item-title ')]//a", $node);
            $category = $this->textContent($xpath, ".//*[contains(concat(' ', normalize-space(@class), ' '), ' tt-posl-item-category ')]", $node);
            $videoUrl = trim((string) ($xpath->query(".//*[contains(concat(' ', normalize-space(@class), ' '), ' tt-posl-item-title ')]//a", $node)?->item(0)?->attributes?->getNamedItem('href')?->nodeValue ?? ''));
            $poster = trim((string) ($xpath->query('.//video', $node)?->item(0)?->attributes?->getNamedItem('poster')?->nodeValue ?? ''));

            $previewMp4 = '';
            $previewWebm = '';

            foreach ($xpath->query('.//video/source', $node) ?? [] as $sourceNode) {
                if (! $sourceNode instanceof DOMElement) {
                    continue;
                }

                $path = trim((string) ($sourceNode->attributes?->getNamedItem('src')?->nodeValue ?? ''));
                $type = trim((string) ($sourceNode->attributes?->getNamedItem('type')?->nodeValue ?? ''));

                if ($path === '') {
                    continue;
                }

                if ($previewMp4 === '' && Str::contains($type, 'mp4')) {
                    $previewMp4 = $path;
                }

                if ($previewWebm === '' && Str::contains($type, 'webm')) {
                    $previewWebm = $path;
                }
            }

            if ($title === '' || $videoUrl === '') {
                continue;
            }

            $normalizedVideoUrl = $this->normalizeAssetPath($videoUrl);
            $platform = GalleryVideo::resolvePlatform(null, $normalizedVideoUrl);

            $items[] = [
                'title' => $title,
                'slug' => GalleryVideo::uniqueSlug($title),
                'excerpt' => null,
                'description' => null,
                'platform' => $platform,
                'video_url' => $normalizedVideoUrl,
                'embed_url' => GalleryVideo::buildEmbedUrl($normalizedVideoUrl, $platform),
                'thumbnail_url' => $this->normalizeAssetPath($poster),
                'preview_video_url' => $this->normalizeAssetPath($previewMp4),
                'preview_video_webm_url' => $this->normalizeAssetPath($previewWebm),
                'category_key' => $category !== '' ? Str::slug($category) : null,
                'category_label' => $category !== '' ? $category : null,
                'status' => GalleryVideo::STATUS_PUBLISHED,
                'sort_order' => $sortOrder,
                'is_featured' => $sortOrder === 0,
                'published_at' => now(),
                'legacy_source' => self::LEGACY_SOURCE,
                'imported_hash' => sha1(implode('|', [
                    self::LEGACY_SOURCE,
                    $title,
                    $normalizedVideoUrl,
                    $category,
                    $sortOrder,
                ])),
                'created_by' => null,
                'updated_by' => null,
            ];

            $sortOrder++;
        }

        return $items;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function fallbackItems(): array
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
                'title' => 'Gamers Assembly 2025',
                'category_label' => 'LAN',
                'video_url' => 'https://youtu.be/I1o44CVvCFA?si=AQtilTw4pJRt3kJu',
                'preview_video_url' => '/template/assets/vids/interview-GA-2025.mp4',
                'preview_video_webm_url' => '/template/assets/vids/interview-GA-2025.webm',
            ],
            [
                'title' => 'Bootcamp GC',
                'category_label' => 'Esport',
                'video_url' => 'https://youtu.be/80qtJPHgmqY',
                'preview_video_url' => '/template/assets/vids/bootcamp feminin.mp4',
                'preview_video_webm_url' => '/template/assets/vids/bootcamp feminin.webm',
            ],
            [
                'title' => 'LAN TGF',
                'category_label' => 'Esport',
                'video_url' => 'https://youtu.be/mWA_KWJfFU0',
                'preview_video_url' => '/template/assets/vids/Interview_Equipe_Rocket-league - Trim.mp4',
                'preview_video_webm_url' => '/template/assets/vids/Interview_Equipe_Rocket-league - Trim.webm',
            ],
            [
                'title' => 'Interview',
                'category_label' => 'Lyon Esport',
                'video_url' => 'https://youtube.com/shorts/5TNjRatspIc?feature=share',
                'preview_video_url' => '/template/assets/vids/interview-LyonEsport.mp4',
                'preview_video_webm_url' => '/template/assets/vids/interview-LyonEsport.webm',
            ],
            [
                'title' => 'Conférence',
                'category_label' => 'Événement',
                'video_url' => 'https://youtu.be/iXSCfTEAs_0',
                'preview_video_url' => '/template/assets/vids/Retour Yusoh (1).mp4',
                'preview_video_webm_url' => '/template/assets/vids/Retour Yusoh (1).webm',
            ],
            [
                'title' => 'Intervention',
                'category_label' => 'Talk',
                'video_url' => 'https://youtu.be/iXSCfTEAs_0',
                'preview_video_url' => '/template/assets/vids/PYUSOH 1 - Trim.mp4',
                'preview_video_webm_url' => '/template/assets/vids/PYUSOH 1 - Trim.webm',
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
                'legacy_source' => self::LEGACY_SOURCE,
                'imported_hash' => sha1(implode('|', [
                    self::LEGACY_SOURCE,
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

    private function textContent(DOMXPath $xpath, string $expression, DOMElement $context): string
    {
        $value = trim((string) ($xpath->query($expression, $context)?->item(0)?->textContent ?? ''));

        return preg_replace('/\s+/', ' ', $value) ?: '';
    }

    private function normalizeAssetPath(?string $path): ?string
    {
        $path = trim((string) $path);
        if ($path === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, '/assets/')) {
            return '/template'.$path;
        }

        if (Str::startsWith($path, 'assets/')) {
            return '/template/'.$path;
        }

        return $path;
    }
}