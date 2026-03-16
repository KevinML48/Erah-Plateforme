<?php

namespace App\Models;

use App\Support\MediaStorage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryVideo extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

    public const PLATFORM_YOUTUBE = 'youtube';
    public const PLATFORM_TWITCH = 'twitch';
    public const PLATFORM_OTHER = 'other';

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'description',
        'platform',
        'video_url',
        'embed_url',
        'thumbnail_url',
        'preview_video_url',
        'preview_video_webm_url',
        'category_key',
        'category_label',
        'status',
        'sort_order',
        'is_featured',
        'published_at',
        'legacy_source',
        'imported_hash',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeOrdered($query)
    {
        return $query
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->orderByDesc('id');
    }

    public function scopePublished($query)
    {
        return $query
            ->where('status', self::STATUS_PUBLISHED)
            ->where(function ($inner): void {
                $inner->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function scopePublishedVisible($query)
    {
        return $query->published()->ordered();
    }

    public function getResolvedEmbedUrlAttribute(): ?string
    {
        if (filled($this->embed_url)) {
            return (string) $this->embed_url;
        }

        return self::buildEmbedUrl((string) $this->video_url, (string) $this->platform);
    }

    public function getResolvedThumbnailUrlAttribute(): ?string
    {
        return $this->resolveManagedMediaUrl($this->thumbnail_url, 'thumbnail');
    }

    public function getResolvedPreviewVideoUrlAttribute(): ?string
    {
        return $this->resolveManagedMediaUrl($this->preview_video_url, 'preview');
    }

    private function resolveManagedMediaUrl(?string $value, string $type): ?string
    {
        if (! filled($value)) {
            return null;
        }

        $mediaValue = (string) $value;

        if (Str::startsWith($mediaValue, ['http://', 'https://'])) {
            return $mediaValue;
        }

        if ($type === 'thumbnail' && Str::startsWith($mediaValue, '/storage/gallery-videos/thumbnails/')) {
            return $this->buildManagedMediaUrl(Str::after($mediaValue, '/storage/'), $type);
        }

        if ($type === 'preview' && Str::startsWith($mediaValue, '/storage/gallery-videos/previews/')) {
            return $this->buildManagedMediaUrl(Str::after($mediaValue, '/storage/'), $type);
        }

        if (Str::startsWith($mediaValue, 'gallery-videos/')) {
            return $this->buildManagedMediaUrl($mediaValue, $type);
        }

        if (Str::startsWith($mediaValue, '/')) {
            return $mediaValue;
        }

        return $mediaValue;
    }

    private function buildManagedMediaUrl(string $path, string $type): string
    {
        $disk = MediaStorage::resolveDiskForPath($path, (string) config('filesystems.media_disk', MediaStorage::publicDisk()));

        if ($disk === MediaStorage::publicDisk()) {
            return $type === 'thumbnail'
                ? route('marketing.gallery-video.thumbnail', ['path' => $path])
                : route('marketing.gallery-video.preview', ['path' => $path]);
        }

        return (string) MediaStorage::diskUrl($path, $disk);
    }

    public function getDisplayCategoryLabelAttribute(): string
    {
        if (filled($this->category_label)) {
            return (string) $this->category_label;
        }

        if (filled($this->category_key)) {
            return Str::headline((string) $this->category_key);
        }

        return 'Galerie';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PUBLISHED => 'Publiee',
            self::STATUS_ARCHIVED => 'Archivee',
            default => 'Brouillon',
        };
    }

    public static function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source);
        if ($base === '') {
            $base = 'video-galerie';
        }

        $slug = $base;
        $suffix = 2;

        while (self::query()
            ->when($ignoreId !== null, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    public static function resolvePlatform(?string $platform, string $videoUrl): string
    {
        $normalized = Str::lower(trim((string) $platform));
        if (in_array($normalized, [self::PLATFORM_YOUTUBE, self::PLATFORM_TWITCH, self::PLATFORM_OTHER], true)) {
            return $normalized;
        }

        $url = Str::lower($videoUrl);

        if (Str::contains($url, ['youtu.be', 'youtube.com'])) {
            return self::PLATFORM_YOUTUBE;
        }

        if (Str::contains($url, ['twitch.tv', 'clips.twitch.tv'])) {
            return self::PLATFORM_TWITCH;
        }

        return self::PLATFORM_OTHER;
    }

    public static function buildEmbedUrl(string $videoUrl, ?string $platform = null): ?string
    {
        $videoUrl = trim($videoUrl);
        if ($videoUrl === '') {
            return null;
        }

        $platform = self::resolvePlatform($platform, $videoUrl);

        if ($platform === self::PLATFORM_YOUTUBE) {
            $videoId = self::extractYouTubeId($videoUrl);

            return $videoId ? 'https://www.youtube.com/embed/'.$videoId : null;
        }

        if ($platform === self::PLATFORM_TWITCH) {
            $vodId = self::extractTwitchVideoId($videoUrl);
            if ($vodId) {
                return 'https://player.twitch.tv/?video=v'.$vodId;
            }

            $clipSlug = self::extractTwitchClipSlug($videoUrl);

            return $clipSlug ? 'https://clips.twitch.tv/embed?clip='.$clipSlug : null;
        }

        return null;
    }

    public static function buildThumbnailUrl(string $videoUrl, ?string $platform = null): ?string
    {
        $videoUrl = trim($videoUrl);
        if ($videoUrl === '') {
            return null;
        }

        $platform = self::resolvePlatform($platform, $videoUrl);

        if ($platform === self::PLATFORM_YOUTUBE) {
            $videoId = self::extractYouTubeId($videoUrl);

            return $videoId ? 'https://i.ytimg.com/vi/'.$videoId.'/hqdefault.jpg' : null;
        }

        return null;
    }

    private static function extractYouTubeId(string $videoUrl): ?string
    {
        $parts = parse_url($videoUrl);
        if (! is_array($parts)) {
            return null;
        }

        $host = Str::lower((string) ($parts['host'] ?? ''));
        $path = trim((string) ($parts['path'] ?? ''), '/');

        if ($host === 'youtu.be' && $path !== '') {
            return $path;
        }

        if (Str::contains($host, 'youtube.com')) {
            parse_str((string) ($parts['query'] ?? ''), $query);
            if (filled($query['v'] ?? null)) {
                return (string) $query['v'];
            }

            if (Str::startsWith($path, 'shorts/')) {
                return Str::after($path, 'shorts/');
            }

            if (Str::startsWith($path, 'embed/')) {
                return Str::after($path, 'embed/');
            }
        }

        return null;
    }

    private static function extractTwitchVideoId(string $videoUrl): ?string
    {
        if (preg_match('/twitch\.tv\/videos\/(\d+)/i', $videoUrl, $matches) === 1) {
            return $matches[1];
        }

        if (preg_match('/player\.twitch\.tv\/.*video=v?(\d+)/i', $videoUrl, $matches) === 1) {
            return $matches[1];
        }

        return null;
    }

    private static function extractTwitchClipSlug(string $videoUrl): ?string
    {
        if (preg_match('/clips\.twitch\.tv\/([A-Za-z0-9_-]+)/', $videoUrl, $matches) === 1) {
            return $matches[1];
        }

        if (preg_match('/twitch\.tv\/[A-Za-z0-9_]+\/clip\/([A-Za-z0-9_-]+)/', $videoUrl, $matches) === 1) {
            return $matches[1];
        }

        return null;
    }
}