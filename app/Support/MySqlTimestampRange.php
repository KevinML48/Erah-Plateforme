<?php

namespace App\Support;

use Illuminate\Support\Carbon;

class MySqlTimestampRange
{
    public static function min(): Carbon
    {
        return Carbon::create(1971, 1, 1, 0, 0, 0, config('app.timezone'));
    }

    public static function max(): Carbon
    {
        return Carbon::create(2037, 12, 31, 23, 59, 59, config('app.timezone'));
    }

    public static function clamp(mixed $value): ?Carbon
    {
        if ($value === null) {
            return null;
        }

        $carbon = $value instanceof Carbon
            ? $value->copy()
            : Carbon::parse($value, config('app.timezone'));

        if ($carbon->lt(static::min())) {
            return static::min();
        }

        if ($carbon->gt(static::max())) {
            return static::max();
        }

        return $carbon;
    }
}
