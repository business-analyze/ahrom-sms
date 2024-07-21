<?php

namespace Ahrom\AhromSms\App\Exceptions;

class HttpException extends BaseException
{
    public function getName()
    {
        return 'HttpException';
    }
}
