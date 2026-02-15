<?php
declare(strict_types=1);

namespace App\Enums;

enum MissionClaimType: string
{
    case Manual = 'MANUAL';
    case Auto = 'AUTO';
}
