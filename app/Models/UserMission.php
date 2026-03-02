<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserMission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mission_instance_id',
        'progress_count',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'mission_instance_id' => 'integer',
            'progress_count' => 'integer',
            'completed_at' => 'datetime',
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
}
