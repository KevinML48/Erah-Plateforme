<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class UserMission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mission_instance_id',
        'progress_count',
        'complèted_at',
        'rewarded_at',
        'claimed_at',
        'expired_at',
        'last_tracked_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'mission_instance_id' => 'integer',
            'progress_count' => 'integer',
            'complèted_at' => 'datetime',
            'rewarded_at' => 'datetime',
            'claimed_at' => 'datetime',
            'expired_at' => 'datetime',
            'last_tracked_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function instance(): BelongsTo
    {
        return $this->belongsTo(MissionInstance::class, 'mission_instance_id');
    }

    public function completion(): HasOne
    {
        return $this->hasOne(MissionCompletion::class, 'user_mission_id');
    }

    public function template(): ?MissionTemplate
    {
        return $this->instance?->template;
    }

    public function isExpired(?Carbon $at = null): bool
    {
        $at = $at ?: now();

        if ($this->expired_at !== null) {
            return true;
        }

        $periodEnd = $this->instance?->period_end;

        return $periodEnd ? $at->gt($periodEnd) : false;
    }
}
