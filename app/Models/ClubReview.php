<?php

namespace App\Models;

use Database\Factories\ClubReviewFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClubReview extends Model
{
    /** @use HasFactory<ClubReviewFactory> */
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_HIDDEN = 'hidden';

    public const SOURCE_MEMBER = 'member';
    public const SOURCE_SEED = 'seed';
    public const SOURCE_ADMIN_IMPORT = 'admin_import';

    protected $fillable = [
        'user_id',
        'author_name',
        'author_profile_url',
        'content',
        'status',
        'is_featured',
        'source',
        'display_order',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'is_featured' => 'boolean',
            'display_order' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    protected static function newFactory(): ClubReviewFactory
    {
        return ClubReviewFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_PUBLISHED)
            ->whereNotNull('published_at');
    }

    public function scopeVisibleOnHome(Builder $query): Builder
    {
        return $query->published()->orderByDesc('published_at')->orderByDesc('id');
    }

    /**
     * @return array<int, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_PUBLISHED,
            self::STATUS_HIDDEN,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return [
            self::STATUS_DRAFT => 'Brouillon',
            self::STATUS_PUBLISHED => 'Publie',
            self::STATUS_HIDDEN => 'Masque',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function sourceLabels(): array
    {
        return [
            self::SOURCE_MEMBER => 'Avis membre',
            self::SOURCE_SEED => 'Avis historique',
            self::SOURCE_ADMIN_IMPORT => 'Import admin',
        ];
    }

    public function authorDisplayName(): string
    {
        return (string) ($this->user?->name ?: $this->author_name ?: 'Membre ERAH');
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[$this->status] ?? ucfirst((string) $this->status);
    }

    public function sourceLabel(): string
    {
        return self::sourceLabels()[$this->source] ?? ucfirst((string) $this->source);
    }
}
