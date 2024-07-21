<?php

namespace Ahrom\AhromSms\App\Enums;

use Ahrom\AhromSms\App\Drivers\Kavenegar\KavenegarConnector;
use Ahrom\AhromSms\App\Drivers\Kavenegar\KavenegarMessage;
use Ahrom\AhromSms\App\Drivers\SmsIr\SmsIrConnector;
use Ahrom\AhromSms\App\Drivers\SmsIr\SmsIrMessage;

enum Drivers: string
{
    case KAVENEGAR = 'kavenegar';
    case SMSIR = 'smsir';

    public function connector(): string
    {
        return match ($this) {
            self::KAVENEGAR => KavenegarConnector::class,
            self::SMSIR => SmsIrConnector::class,
        };
    }

    public function message(): string
    {
        return match ($this) {
            self::KAVENEGAR => KavenegarMessage::class,
            self::SMSIR => SmsIrMessage::class
        };
    }
}
