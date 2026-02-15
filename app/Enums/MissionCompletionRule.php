<?php
declare(strict_types=1);

namespace App\Enums;

enum MissionCompletionRule: string
{
    case All = 'ALL';
    case AnyN = 'ANY_N';
}
