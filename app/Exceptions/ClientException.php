<?php

namespace App\Exceptions;

use Throwable;

class ClientException extends \Exception
{
    public function __construct($message = "На сервере произошла ошибка", $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
