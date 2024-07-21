<?php

namespace Ahrom\AhromSms\App\Exceptions;

class DriverNotFoundException extends BaseException
{
    public function getName()
    {
        return 'DriverNotFoundException';
    }
}
