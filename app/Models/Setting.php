<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn (self $setting) => Cache::forget(self::cacheKey((string) $setting->key)));
        static::deleted(fn (self $setting) => Cache::forget(self::cacheKey((string) $setting->key)));
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        return Cache::remember(self::cacheKey($key), now()->addMinutes(30), function () use ($key, $default): mixed {
            $setting = self::query()->where('key', $key)->first();

            return $setting?->value ?? $default;
        });
    }

    public static function cacheKey(string $key): string
    {
        return 'settings:'.$key;
    }
}
