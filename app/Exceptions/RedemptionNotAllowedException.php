<?php
declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class RedemptionNotAllowedException extends RuntimeException
{
    public function __construct(string $message = 'Transition de redemption non autorisee.')
    {
        parent::__construct($message);
    }
}

