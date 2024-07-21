<?php

namespace Ahrom\AhromSms\App\Exceptions;

class ApiException extends BaseException
{
    public function getName()
    {
        return 'ApiException';
    }
}
