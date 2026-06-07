<?php

declare(strict_types=1);

namespace App\Integrations\SmsGateway\SendSms;

interface SendSmsProviderInterface
{
    public function send(string $phone, string $message);
}
