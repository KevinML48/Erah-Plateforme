<?php
declare(strict_types=1);

namespace App\Enums;

enum MatchResult: string
{
    case Win = 'WIN';
    case Lose = 'LOSE';
}
