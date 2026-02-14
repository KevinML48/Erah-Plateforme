<?php
declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class MatchLockedException extends RuntimeException
{
    public function __construct(string $message = 'Le match est verrouille pour les tickets.')
    {
        parent::__construct($message);
    }
}

