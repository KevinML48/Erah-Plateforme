<?php

namespace App\Domain\Notifications\Enums;

enum NotificationCategory: string
{
    case DUEL = 'duel';
    case CLIPS = 'clips';
    case SYSTEM = 'system';
    case MATCH = 'match';
    case BET = 'bet';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $case) => $case->value, self::cases());
    }
}
