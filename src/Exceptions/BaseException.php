<?php

namespace Ahrom\AhromSms\App\Exceptions;

abstract class BaseException extends \RuntimeException
{
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }

    public function errorMessage()
    {
        return "\r\n".$this->getName()."[{$this->code}] : {$this->message}\r\n";
    }

    abstract public function getName();
}
