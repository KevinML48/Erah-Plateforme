<?php
declare(strict_types=1);

namespace App\Enums;

enum SelectionStatus: string
{
    case Pending = 'PENDING';
    case Won = 'WON';
    case Lost = 'LOST';
    case Void = 'VOID';
}

