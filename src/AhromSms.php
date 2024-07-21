<?php

namespace Ahrom\AhromSms\App;

use Ahrom\AhromSms\App\Contracts\DriverConnector;
use Ahrom\AhromSms\App\Contracts\DriverMessage;
use Ahrom\AhromSms\App\Enums\Drivers;
use Ahrom\AhromSms\App\Events\SmsSent;

class AhromSms
{
    protected DriverConnector $driver;

    public function getDefaultSetting(): object
    {
        return $this->driver()::setting();
    }

    public function getDefaultDriver(): DriverConnector
    {
        $connector = Drivers::tryFrom('smsir')->connector();
        // $connector = Drivers::tryFrom(config('chapaar.default'))->connector();

        return new $connector;
    }

    public function send($message): object
    {
        $response = $this->driver($message->getDriver())->send($message);

        SmsSent::dispatchIf(self::shouldStoreToSentMessage(), $response);

        return $response;

    }

    public function verify(DriverMessage $message): object
    {
        $response = $this->driver($message->getDriver())->verify($message);

        SmsSent::dispatchIf(self::shouldStoreToSentMessage(), $response);

        return $response;
    }

    public function account(): object
    {

        return $this->driver()->account();
    }

    public function outbox(int $page_size = 100, int $page_number = 1): object
    {
        return $this->driver()->outbox($page_size, $page_number);
    }

    protected function driver(?Drivers $driver = null): DriverConnector
    {
        $connector = $driver
        ? Drivers::tryFrom($driver->value)->connector()
        : Drivers::tryFrom('smsir')->connector();
        // : Drivers::tryFrom(config('chapaar.default'))->connector();

        return new $connector;
    }

    protected static function shouldStoreToSentMessage(): bool
    {
        return false;
        // return config('chapaar.status');
    }
}
