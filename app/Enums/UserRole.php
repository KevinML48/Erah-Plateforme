<?php
declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case User = 'USER';
    case Admin = 'ADMIN';
}
