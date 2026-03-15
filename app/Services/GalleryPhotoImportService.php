<?php

namespace App\Services;

use App\Models\GalleryPhoto;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class GalleryPhotoImportService
{
    private const LEGACY_SOURCE = 'resources/views/marketing/legacy/galerie-photos-static.blade.php';

    private const FILTER_LABELS = [
        'valorant' => 'Valorant',
        'evenements' => 'Evenements',
        'compétitions' => 'Competitions',
    ];

    public function legacySourceExists(): bool
    {
        return file_exists(base_path(self::LEGACY_SOURCE));
    }

    public function importIfEmpty(): int
    {
        if (! Schema::hasTable('gallery_photos')) {
            return 0;
        }

        if (GalleryPhoto::query()->exists()) {
            return 0;
        }

        return $this->import()['processused'];
    }

    public function import(): array
    {
        if (! Schema::hasTable('gallery_photos') || ! $this->legacySourceExists()) {
            return ['processused' => 0, 'created' => 0, 'updated' => 0, 'skipped' => 0, 'found' => 0];
        }

        $items = $this->extractLegacyItems();
        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($items as $item) {
            $existing = GalleryPhoto::query()->where('imported_hash', $item['imported_hash'])->first();

            if ($existing && $existing->updated_by !== null) {
                $skipped++;
                continue;
            }

            if ($existing) {
                $existing->fill($item)->save();
                $updated++;
                continue;
            }

            GalleryPhoto::query()->create($item);
            $created++;
        }

        return [
            'processused' => $created + $updated,
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

        $html = $this->normalizeBladeForParsing($source);
        $document = new DOMDocument('1.0', 'UTF-8');

        libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="utf-8" ?>'.$html, LIBXML_NOWARNING | LIBXML_NOERROR);
        libxml_clear_errors();

        $xpath = new DOMXPath($document);
        $nodes = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' tt-grid-item ') and contains(concat(' ', normalize-space(@class), ' '), ' isotope-item ')]");

        if ($nodes === false) {
            return [];
        }

        $items = [];
        $sortOrder = 0;

        foreach ($nodes as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $classes = preg_split('/\s+/', trim((string) $node->getAttribute('class'))) ?: [];
            $filterKey = collect($classes)
                ->reject(fn (string $class): bool => in_array($class, ['tt-grid-item', 'isotope-item'], true))
                ->first();

            $title = $this->textContent($xpath, ".//*[contains(concat(' ', normalize-space(@class), ' '), ' pgi-title ')]//a", $node)
                ?: $this->textContent($xpath, ".//*[contains(concat(' ', normalize-space(@class), ' '), ' pgi-title ')]", $node);

            $category = $this->textContent($xpath, ".//*[contains(concat(' ', normalize-space(@class), ' '), ' pgi-category ')]", $node);
            $cursorLabel = trim((string) ($xpath->query(".//a[contains(concat(' ', normalize-space(@class), ' '), ' pgi-image-wrap ')]", $node)?->item(0)?->attributes?->getNamedItem('data-cursor')?->nodeValue ?? ''));
            $imagePath = trim((string) ($xpath->query(".//img", $node)?->item(0)?->attributes?->getNamedItem('src')?->nodeValue ?? ''));
            $videoPath = trim((string) ($xpath->query(".//video/source", $node)?->item(0)?->attributes?->getNamedItem('src')?->nodeValue ?? ''));
            $isVideo = $videoPath !== '';
            $mediaPath = $isVideo ? $videoPath : $imagePath;

            if ($mediaPath === '') {
                continue;
            }

            $items[] = [
                'title' => $title !== '' ? $title : null,
                'description' => null,
                'image_path' => $isVideo ? null : $mediaPath,
                'video_path' => $isVideo ? $mediaPath : null,
                'media_type' => $isVideo ? GalleryPhoto::MEDIA_TYPE_VIDEO : GalleryPhoto::MEDIA_TYPE_IMAGE,
                'alt_text' => $title !== '' ? $title : 'ERAH galerie',
                'filter_key' => $filterKey ?: null,
                'filter_label' => $filterKey ? (self::FILTER_LABELS[$filterKey] ?? Str::headline($filterKey)) : null,
                'category_label' => $category !== '' ? $category : null,
                'cursor_label' => $cursorLabel !== '' ? $cursorLabel : null,
                'sort_order' => $sortOrder++,
                'is_active' => true,
                'published_at' => now(),
                'storage_disk' => null,
                'media_mime_type' => $this->guessMimeType($mediaPath, $isVideo),
                'media_size' => null,
                'legacy_source' => self::LEGACY_SOURCE,
                'imported_hash' => sha1(implode('|', [
                    self::LEGACY_SOURCE,
                    $mediaPath,
                    $title,
                    (string) $filterKey,
                    (string) $category,
                ])),
                'created_by' => null,
                'updated_by' => null,
            ];
        }

        return $items;
    }

    private function normalizeBladeForParsing(string $source): string
    {
        return str_replace(
            ['@extends(\'marketing.layouts.template\')', '@section(\'content\')', '@section(\'page_styles\')', '@section(\'page_scripts\')', '@verbatim', '@endverbatim', '@endsection'],
            '',
            $source
        );
    }

    private function textContent(DOMXPath $xpath, string $expression, DOMElement $context): string
    {
        $value = trim((string) ($xpath->query($expression, $context)?->item(0)?->textContent ?? ''));

        return preg_replace('/\s+/', ' ', $value) ?: '';
    }

    private function guessMimeType(string $path, bool $isVideo): string
    {
        $extension = Str::lower(pathinfo($path, PATHINFO_EXTENSION));

        return match (true) {
            $isVideo && $extension === 'mp4' => 'video/mp4',
            $isVideo && $extension === 'webm' => 'video/webm',
            $extension === 'png' => 'image/png',
            $extension === 'webp' => 'image/webp',
            in_array($extension, ['jpg', 'jpeg'], true) => 'image/jpeg',
            default => $isVideo ? 'video/mp4' : 'image/jpeg',
        };
    }
}
