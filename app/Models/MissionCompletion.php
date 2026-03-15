<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionCompletion extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'user_mission_id',
        'complèted_at',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'user_mission_id' => 'integer',
            'complèted_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userMission(): BelongsTo
    {
        return $this->belongsTo(UserMission::class, 'user_mission_id');
    }
}
