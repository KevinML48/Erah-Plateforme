<?php
declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class StakeLimitException extends RuntimeException
{
    public function __construct(string $message = 'Mise invalide.')
    {
        parent::__construct($message);
    }
}

