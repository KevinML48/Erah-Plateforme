<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'intro',
        'pass_score',
        'max_attempts_per_user',
        'reward_points',
        'xp_reward',
        'is_active',
        'starts_at',
        'ends_at',
        'mission_template_id',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'pass_score' => 'integer',
            'max_attempts_per_user' => 'integer',
            'reward_points' => 'integer',
            'xp_reward' => 'integer',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'mission_template_id' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $builder): void {
                $builder->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $builder): void {
                $builder->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('sort_order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function missionTemplate(): BelongsTo
    {
        return $this->belongsTo(MissionTemplate::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
