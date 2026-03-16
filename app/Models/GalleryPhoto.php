<?php

namespace App\Models;

use App\Support\MediaStorage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class GalleryPhoto extends Model
{
    use HasFactory;

    public const MEDIA_TYPE_IMAGE = 'image';
    public const MEDIA_TYPE_VIDEO = 'video';

    protected $fillable = [
        'title',
        'description',
        'image_path',
        'video_path',
        'media_type',
        'alt_text',
        'filter_key',
        'filter_label',
        'category_label',
        'cursor_label',
        'sort_order',
        'is_active',
        'published_at',
        'storage_disk',
        'media_mime_type',
        'media_size',
        'legacy_source',
        'imported_hash',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'published_at' => 'datetime',
            'sort_order' => 'integer',
            'media_size' => 'integer',
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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublished($query)
    {
        return $query->where(function ($inner): void {
            $inner->whereNull('published_at')
                ->orWhere('published_at', '<=', now());
        });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderByDesc('id');
    }

    public function scopeVisible($query)
    {
        return $query->active()->published()->ordered();
    }

    public function getIsVideoAttribute(): bool
    {
        return $this->media_type === self::MEDIA_TYPE_VIDEO;
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->resolveMediaPath($this->image_path);
    }

    public function getVideoUrlAttribute(): ?string
    {
        return $this->resolveMediaPath($this->video_path);
    }

    public function getPrimaryMediaUrlAttribute(): ?string
    {
        return $this->is_video ? $this->video_url : $this->image_url;
    }

    public function getDisplayAltTextAttribute(): string
    {
        return trim((string) ($this->alt_text ?: $this->title ?: 'ERAH galerie'));
    }

    private function resolveMediaPath(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $path = (string) $path;

        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return $path;
        }

        return MediaStorage::url($path, filled($this->storage_disk) ? (string) $this->storage_disk : null);
    }
}
