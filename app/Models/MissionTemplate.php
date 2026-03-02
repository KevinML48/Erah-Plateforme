<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MissionTemplate extends Model
{
    use HasFactory;

    public const SCOPE_ONCE = 'once';
    public const SCOPE_DAILY = 'daily';
    public const SCOPE_WEEKLY = 'weekly';
    public const SCOPE_EVENT_WINDOW = 'event_window';

    protected $fillable = [
        'key',
        'title',
        'description',
        'event_type',
        'target_count',
        'scope',
        'start_at',
        'end_at',
        'constraints',
        'rewards',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'target_count' => 'integer',
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'constraints' => 'array',
            'rewards' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function scopes(): array
    {
        return [
            self::SCOPE_ONCE,
            self::SCOPE_DAILY,
            self::SCOPE_WEEKLY,
            self::SCOPE_EVENT_WINDOW,
        ];
    }

    public function instances(): HasMany
    {
        return $this->hasMany(MissionInstance::class, 'mission_template_id');
    }
}
