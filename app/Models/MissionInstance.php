<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MissionInstance extends Model
{
    use HasFactory;

    protected $fillable = [
        'mission_template_id',
        'period_start',
        'period_end',
    ];

    protected function casts(): array
    {
        return [
            'mission_template_id' => 'integer',
            'period_start' => 'datetime',
            'period_end' => 'datetime',
        ];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(MissionTemplate::class, 'mission_template_id');
    }

    public function userMissions(): HasMany
    {
        return $this->hasMany(UserMission::class, 'mission_instance_id');
    }
}
