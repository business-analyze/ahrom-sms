<?php

namespace Ahrom\AhromSms\App\Drivers\SmsIr;

use Ahrom\AhromSms\App\Contracts\DriverMessage;
use Ahrom\AhromSms\App\Enums\Drivers;

class SmsIrMessage implements DriverMessage
{
    public Drivers $driver = Drivers::SMSIR;

    protected string $content = '';

    protected string $from = '';

    protected array|string $to = '';

    protected int $template = 0;

    protected array $tokens = [];

    protected ?string $date = null;

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function setFrom($from): self
    {
        $this->from = $from;

        return $this;
    }

    public function getTo(): array|string
    {
        return $this->to;
    }

    public function setTo(array|string $to): static
    {
        if (is_array($to) && $this->getTemplate()) {
            $to = reset($to);
        }

        $this->to = $to;

        return $this;
    }

    public function getTemplate(): int
    {
        return (int) $this->template;
    }

    public function setTemplate($template): self
    {
        $this->template = $template;

        return $this;
    }

    public function getTokens(): array
    {
        return $this->tokens;
    }

    public function setTokens(array $tokens): self
    {
        $token_array = [];
        foreach ($tokens as $key => $token) {
            $token_array[] = ['name' => $key, 'value' => $token];
        }

        $this->tokens = $token_array;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    public function getDriver(): Drivers
    {
        return $this->driver;
    }
}
