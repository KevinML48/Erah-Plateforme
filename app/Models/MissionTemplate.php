<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class MissionTemplate extends Model
{
    use HasFactory;

    public const SCOPE_ONCE = 'once';
    public const SCOPE_DAILY = 'daily';
    public const SCOPE_WEEKLY = 'weekly';
    public const SCOPE_MONTHLY = 'monthly';
    public const SCOPE_EVENT_WINDOW = 'event_window';

    protected $fillable = [
        'key',
        'title',
        'short_description',
        'description',
        'long_description',
        'event_type',
        'target_count',
        'scope',
        'category',
        'type',
        'difficulty',
        'estimated_minutes',
        'is_discovery',
        'is_featured',
        'is_repeatable',
        'requires_claim',
        'sort_order',
        'start_at',
        'end_at',
        'constraints',
        'rewards',
        'prerequisites',
        'icon',
        'badge_label',
        'ui_meta',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'target_count' => 'integer',
            'estimated_minutes' => 'integer',
            'sort_order' => 'integer',
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'constraints' => 'array',
            'rewards' => 'array',
            'prerequisites' => 'array',
            'ui_meta' => 'array',
            'is_discovery' => 'boolean',
            'is_featured' => 'boolean',
            'is_repeatable' => 'boolean',
            'requires_claim' => 'boolean',
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
            self::SCOPE_MONTHLY,
            self::SCOPE_EVENT_WINDOW,
        ];
    }

    public function instances(): HasMany
    {
        return $this->hasMany(MissionInstance::class, 'mission_template_id');
    }

    public function focuses(): HasMany
    {
        return $this->hasMany(UserMissionFocus::class, 'mission_template_id');
    }

    public function normalizedEventType(): string
    {
        return self::normalizeEventType((string) $this->event_type);
    }

    /**
     * @return array{xp: int, points: int}
     */
    public function normalizedRewards(): array
    {
        $rewards = is_array($this->rewards) ? $this->rewards : [];

        $points = (int) (
            $rewards['points']
            ?? $rewards['reward_points']
            ?? $rewards['reward_points_amount']
            ?? 0
        );
        $points += (int) ($rewards['bet_points'] ?? $rewards['bet_points_amount'] ?? 0);

        return [
            'xp' => max(0, (int) ($rewards['xp'] ?? $rewards['xp_amount'] ?? 0)),
            'points' => max(0, $points),
        ];
    }

    public function shortDescription(): string
    {
        return (string) ($this->short_description ?: $this->description ?: $this->title);
    }

    public function longDescription(): ?string
    {
        return $this->long_description ?: $this->description;
    }

    public function isAvailableAt(?Carbon $at = null): bool
    {
        $at = $at ?: now();

        if (! $this->is_active) {
            return false;
        }

        if ($this->start_at && $at->lt($this->start_at)) {
            return false;
        }

        if ($this->end_at && $at->gt($this->end_at)) {
            return false;
        }

        return true;
    }

    public static function normalizeEventType(string $eventType): string
    {
        return (string) str($eventType)
            ->trim()
            ->lower()
            ->replace([' ', '-', '_'], '.')
            ->replace('..', '.')
            ->trim('.');
    }
}
