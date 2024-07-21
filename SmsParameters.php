<?php
namespace Ahrom\AhromSms;

class SmsParameters
{
    public $to;
    public $template;
    public $tokens;

    public function __construct($to = null, $template = null, $tokens = [])
    {
        $this->to = $to;
        $this->template = $template;
        $this->tokens = $tokens;
    }
}
