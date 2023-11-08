<?php

namespace App\Exceptions;

use App\Helpers\BigBlueButtonApiResponse;
use Exception;

class JoinMeetingException extends Exception
{
    public function __construct(BigBlueButtonApiResponse $response, int $code = 0, ?\Throwable $previous = null)
    {
        $message = $response->getBbbMessage();
        parent::__construct($message, $code, $previous);
    }
}
