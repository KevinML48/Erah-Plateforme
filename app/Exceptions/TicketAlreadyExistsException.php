<?php
declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class TicketAlreadyExistsException extends RuntimeException
{
    public function __construct(string $message = 'Un ticket existe deja pour ce match.')
    {
        parent::__construct($message);
    }
}

