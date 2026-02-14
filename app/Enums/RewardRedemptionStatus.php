<?php
declare(strict_types=1);

namespace App\Enums;

enum RewardRedemptionStatus: string
{
    case Pending = 'PENDING';
    case Approved = 'APPROVED';
    case Rejected = 'REJECTED';
    case Shipped = 'SHIPPED';
    case Cancelled = 'CANCELLED';
}

