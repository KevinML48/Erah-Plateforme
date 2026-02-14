<?php
declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class MatchAlreadyCompletedException extends RuntimeException
{
    public function __construct(string $message = 'Match is already completed.')
    {
        parent::__construct($message);
    }
}
