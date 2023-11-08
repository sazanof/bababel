<?php

namespace App\Exceptions;

use Exception;

class AlreadyJoinedException extends Exception
{
    public function __construct(int $code = 409, ?\Throwable $previous = null)
    {
        $message = __('exceptions.join.duplicate');
        parent::__construct($message, $code, $previous);
    }
}
