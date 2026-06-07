<?php

namespace Tests;

use App\Integrations\EmailGateway\SendEmail\DummySendEmailProvider;
use App\Integrations\EmailGateway\SendEmail\SendEmailProviderInterface;
use App\Integrations\SmsGateway\SendSms\DummySendSmsProvider;
use App\Integrations\SmsGateway\SendSms\SendSmsProviderInterface;
use App\Queue\Mock\AmqpMessageStub;
use App\Queue\Mock\DummyProducer;
use App\Queue\ProducerInterface;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function iAmAuthenticatedServerApiUser()
    {
        $this->withBasicAuth(env('SERVER_API_EXAMPLE_USERNAME'), env('SERVER_API_EXAMPLE_PASSWORD'));
    }

    protected function mockAmqp(): void
    {
        $this->instance(
            ProducerInterface::class, new DummyProducer()
        );
    }

    protected function mockEmailSendService(): void
    {
        $this->instance(
            SendEmailProviderInterface::class, new DummySendEmailProvider()
        );
    }

    protected function mockSmsSendService(): void
    {
        $this->instance(
            SendSmsProviderInterface::class, new DummySendSmsProvider()
        );
    }
}
