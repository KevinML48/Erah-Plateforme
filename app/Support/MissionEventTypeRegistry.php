<?php

namespace App\Support;

final class MissionEventTypeRegistry
{
    /**
     * @return array<int, string>
     */
    public static function supported(): array
    {
        return [
            'login.daily',
            'profile.completed',
            'clip.view',
            'clip.like',
            'clip.comment',
            'clip.share',
            'clip.favorite',
            'bet.placed',
            'bet.won',
            'duel.sent',
            'duel.accepted',
            'duel.play',
            'duel.win',
            'quiz.attempt',
            'quiz.pass',
            'live_code.redeem',
            'shop.purchase',
            'supporter.monthly',
        ];
    }

    public static function normalize(string $eventType): string
    {
        return (string) str($eventType)
            ->trim()
            ->lower()
            ->replace([' ', '-', '_'], '.')
            ->replace('..', '.')
            ->trim('.');
    }

    public static function isSupported(string $eventType): bool
    {
        return in_array(self::normalize($eventType), self::supported(), true);
    }
}

