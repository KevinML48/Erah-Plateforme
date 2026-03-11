<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMissionFocus extends Model
{
    protected $table = 'user_mission_focuses';

    protected $fillable = [
        'user_id',
        'mission_template_id',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'mission_template_id' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(MissionTemplate::class, 'mission_template_id');
    }
}
