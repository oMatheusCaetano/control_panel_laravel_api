<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class NoModelClassDefinedException extends Exception
{
    public function __construct(
        string $message = 'No model class has been defined.',
        int $code = 400,
        ?Throwable $previous = null
    ) {
        $this->message = $message;
        $this->code = $code;
        $this->previous = $previous;
    }
}
