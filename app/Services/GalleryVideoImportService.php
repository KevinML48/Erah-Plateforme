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
        if (! Schema::hasTable('gallery_videos') || ! $this->legacySourceExists()) {
            return ['processed' => 0, 'created' => 0, 'updated' => 0, 'skipped' => 0, 'found' => 0];
        }

        $items = $this->extractLegacyItems();
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