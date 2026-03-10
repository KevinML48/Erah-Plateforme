<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HelpArticle extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';

    protected $fillable = [
        'help_category_id',
        'title',
        'slug',
        'summary',
        'body',
        'short_answer',
        'keywords',
        'tutorial_video_url',
        'cta_label',
        'cta_url',
        'status',
        'is_featured',
        'is_faq',
        'sort_order',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'keywords' => 'array',
            'is_featured' => 'boolean',
            'is_faq' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_PUBLISHED,
        ];
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(HelpCategory::class, 'help_category_id');
    }
}
