<?php
declare(strict_types=1);

namespace App\Models;

use App\Enums\MissionClaimType;
use App\Enums\MissionCompletionRule;
use App\Enums\MissionRecurrence;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mission extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'points_reward',
        'recurrence',
        'completion_rule',
        'any_n',
        'claim_type',
        'starts_at',
        'ends_at',
        'is_active',
        'max_claims_total',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'points_reward' => 'integer',
            'is_active' => 'boolean',
            'max_claims_total' => 'integer',
            'any_n' => 'integer',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'recurrence' => MissionRecurrence::class,
            'completion_rule' => MissionCompletionRule::class,
            'claim_type' => MissionClaimType::class,
        ];
    }

    public function steps(): HasMany
    {
        return $this->hasMany(MissionStep::class)->orderBy('order');
    }

    public function progresses(): HasMany
    {
        return $this->hasMany(MissionProgress::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActiveNow(Builder $query, ?Carbon $now = null): Builder
    {
        $now ??= now();

        return $query
            ->where('is_active', true)
            ->where(function (Builder $builder) use ($now): void {
                $builder->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function (Builder $builder) use ($now): void {
                $builder->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            });
    }

    public function isCurrentlyActive(?Carbon $now = null): bool
    {
        $now ??= now();

        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at !== null && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at !== null && $now->gt($this->ends_at)) {
            return false;
        }

        return true;
    }

    public function getPeriodKey(?Carbon $now = null): string
    {
        $now ??= now();

        return match ($this->recurrence) {
            MissionRecurrence::OneTime => 'ALL',
            MissionRecurrence::Daily => $now->format('Y-m-d'),
            MissionRecurrence::Weekly => $now->format('o-\\WW'),
            MissionRecurrence::Monthly => $now->format('Y-m'),
        };
    }

    public function getTargetStepsCount(): int
    {
        $stepsCount = max(1, $this->steps->count());

        if ($this->completion_rule === MissionCompletionRule::AnyN) {
            $threshold = (int) ($this->any_n ?? 1);

            return max(1, min($threshold, $stepsCount));
        }

        return $stepsCount;
    }
}
