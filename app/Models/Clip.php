<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Clip extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'video_url',
        'thumbnail_url',
        'is_published',
        'published_at',
        'likes_count',
        'favorites_count',
        'comments_count',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'likes_count' => 'integer',
            'favorites_count' => 'integer',
            'comments_count' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->whereNotNull('published_at');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(ClipLike::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(ClipFavorite::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ClipComment::class);
    }

    public function shares(): HasMany
    {
        return $this->hasMany(ClipShare::class);
    }

    public function voteEntries(): HasMany
    {
        return $this->hasMany(ClipVoteEntry::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(ClipVote::class);
    }

    public function supporterReactions(): HasMany
    {
        return $this->hasMany(ClipSupporterReaction::class);
    }
}
