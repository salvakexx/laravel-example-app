<?php

declare(strict_types=1);

namespace App\Integrations\EmailGateway\SendEmail;

interface SendEmailProviderInterface
{
    public function send(string $email, string $topic, string $message);
}
