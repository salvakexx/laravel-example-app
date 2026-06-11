<?php

declare(strict_types=1);

namespace App\Integrations\SmsGateway\SendSms;

class DummySendSmsProvider implements SendSmsProviderInterface
{
    public function send(string $phone, string $message) {}
}
