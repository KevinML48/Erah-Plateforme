<?php
declare(strict_types=1);

namespace App\Enums;

enum MarketStatus: string
{
    case Open = 'OPEN';
    case Locked = 'LOCKED';
    case Settled = 'SETTLED';
    case Void = 'VOID';
}

