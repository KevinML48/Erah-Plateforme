<?php
declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class InvalidTicketSelectionException extends RuntimeException
{
    public function __construct(string $message = 'Selections du ticket invalides.')
    {
        parent::__construct($message);
    }
}

