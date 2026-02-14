<?php
declare(strict_types=1);

namespace App\Enums;

enum MatchStatus: string
{
    case Draft = 'DRAFT';
    case Open = 'OPEN';
    case Locked = 'LOCKED';
    case Live = 'LIVE';
    case Completed = 'COMPLETED';
    case Cancelled = 'CANCELLED';
}
