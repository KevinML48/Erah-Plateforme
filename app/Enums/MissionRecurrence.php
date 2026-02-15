<?php
declare(strict_types=1);

namespace App\Enums;

enum MissionRecurrence: string
{
    case OneTime = 'ONE_TIME';
    case Daily = 'DAILY';
    case Weekly = 'WEEKLY';
    case Monthly = 'MONTHLY';
}
