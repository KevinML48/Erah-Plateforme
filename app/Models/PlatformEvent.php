<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PlatformEvent extends Model
{
    protected $table = 'events';

    protected $fillable = [
        'key',
        'title',
        'description',
        'type',
        'status',
        'is_active',
        'starts_at',
        'ends_at',
        'config',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'config' => 'array',
        ];
    }

    public function scopeActiveWindow(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where('status', 'published')
            ->where(function (Builder $builder): void {
                $builder->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $builder): void {
                $builder->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }
}
