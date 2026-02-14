<?php
declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class RedemptionAlreadyProcessedException extends RuntimeException
{
    public function __construct(string $message = 'Redemption deja traitee.')
    {
        parent::__construct($message);
    }
}

