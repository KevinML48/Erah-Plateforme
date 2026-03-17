<?php

namespace App\Support;

class QueueRouting
{
    public static function activeConnection(): string
    {
        return (string) config('queue.default', 'sync');
    }

    public static function activeQueue(): string
    {
        $connection = self::activeConnection();
        $configuredQueue = config("queue.connections.{$connection}.queue");

        return is_string($configuredQueue) && $configuredQueue !== ''
            ? $configuredQueue
            : 'default';
    }
}