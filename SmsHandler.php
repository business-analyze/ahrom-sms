<?php

namespace Ahrom\AhromSms;

use Ahrom\AhromSms\App\AhromSms;
use Ahrom\AhromSms\App\SmsMessage;
use Ahrom\AhromSms\App\Enums\Drivers;
use Ahrom\AhromSms\SmsParameters;

class SmsHandler
{
    private $ahromSms;
    
    public function __construct()
    {
        $this->ahromSms = new AhromSms();
    }
    
    public function sendFastSmsByTemplate(SmsParameters $params)
    {
        $message = (new SmsMessage())
            ->driver(Drivers::SMSIR)
            ->setTo($params->to)
            ->setTemplate($params->template)
            ->setTokens($params->tokens);

        $response = $this->ahromSms->verify($message);

        return $response;
    }
}