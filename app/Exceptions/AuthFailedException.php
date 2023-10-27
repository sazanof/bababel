<?php

namespace App\Exceptions;

use Exception;

class AuthFailedException extends Exception
{
    public function __construct(int $code = 0, ?\Throwable $previous = null)
    {
        $message = __('auth.failed');
        parent::__construct($message, $code, $previous);
    }
}
