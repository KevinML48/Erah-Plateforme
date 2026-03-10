<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HelpCategory extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'intro',
        'icon',
        'landing_bucket',
        'tutorial_video_url',
        'status',
        'sort_order',
    ];

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

    public function articles(): HasMany
    {
        return $this->hasMany(HelpArticle::class)->orderBy('sort_order')->orderBy('title');
    }

    public function publishedArticles(): HasMany
    {
        return $this->articles()->where('status', HelpArticle::STATUS_PUBLISHED);
    }
}
