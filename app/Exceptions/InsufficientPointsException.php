<?php
declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class InsufficientPointsException extends RuntimeException
{
    public function __construct(string $message = 'Insufficient points balance.')
    {
        parent::__construct($message);
    }
}

