<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LiveCode extends Model
{
    protected $fillable = [
        'code',
        'label',
        'description',
        'status',
        'reward_points',
        'bet_points',
        'xp_reward',
        'usage_limit',
        'per_user_limit',
        'expires_at',
        'mission_template_id',
        'created_by',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'reward_points' => 'integer',
            'bet_points' => 'integer',
            'xp_reward' => 'integer',
            'usage_limit' => 'integer',
            'per_user_limit' => 'integer',
            'expires_at' => 'datetime',
            'mission_template_id' => 'integer',
            'created_by' => 'integer',
            'meta' => 'array',
        ];
    }

    public function scopeRedeemable(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->where(function (Builder $builder): void {
                $builder->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            });
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(LiveCodeRedemption::class);
    }

    public function missionTemplate(): BelongsTo
    {
        return $this->belongsTo(MissionTemplate::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
