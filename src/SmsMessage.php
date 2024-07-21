<?php

namespace Ahrom\AhromSms\App;

use Ahrom\AhromSms\App\Contracts\DriverMessage;
use Ahrom\AhromSms\App\Enums\Drivers;

class SmsMessage
{
    public function driver($driver = null): DriverMessage
    {
        $driver_message = $driver
        ? Drivers::tryFrom($driver->value)->message()
        : Drivers::tryFrom('smsir')->message();
        // : Drivers::tryFrom(config('chapaar.default'))->message();

        return new $driver_message;
    }
}
