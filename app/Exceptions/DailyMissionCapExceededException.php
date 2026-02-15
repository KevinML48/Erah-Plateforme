<?php
declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class DailyMissionCapExceededException extends RuntimeException
{
    protected $message = 'Daily mission points cap exceeded.';
}
