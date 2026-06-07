<?php

namespace Feature\Communications;

use App\Features\Communications\Domain\Enum\ChannelType;
use App\Queue\ExchangesAndQueuesEnum;
use App\Queue\Mock\DummyProducer;
use App\Queue\ProducerInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SendNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockAmqp();
    }

    public function testValidationSendNotification()
    {
        $this->iAmAuthenticatedServerApiUser();
        $response = $this->json(
            'POST',
            'server-api/send-notifications/bulk/',
        );

        $this->assertEquals(422, $response->getStatusCode());
    }

    public function testValidSendNotification()
    {
        $this->iAmAuthenticatedServerApiUser();
        $response = $this->json(
            'POST',
            'server-api/send-notifications/bulk/',
            [
                'message' => fake()->sentence(),
                'channel' => ChannelType::SMS->value,
                'recipients' => [
                    fake()->e164PhoneNumber(),
                    fake()->e164PhoneNumber(),
                    fake()->e164PhoneNumber(),
                ],
            ]
        );

        $this->assertDatabaseCount('notifications', 3);

        /* @var $amqpMock DummyProducer */
        $amqpMock = app(ProducerInterface::class);
        $this->assertEquals(3, count($amqpMock->getMessages(ExchangesAndQueuesEnum::COMMUNICATIONS_TOPIC->value)));

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testIdempotencyCheck()
    {
        $this->iAmAuthenticatedServerApiUser();

        $sameRequest = [
            'message' => fake()->sentence(),
            'channel' => ChannelType::SMS->value,
            'recipients' => [
                fake()->e164PhoneNumber(),
                fake()->e164PhoneNumber(),
                fake()->e164PhoneNumber(),
            ],
        ];

        $response = $this->json(
            'POST',
            'server-api/send-notifications/bulk/',
            $sameRequest
        );
        $sameResponse = $this->json(
            'POST',
            'server-api/send-notifications/bulk/',
            $sameRequest
        );

        $this->assertDatabaseCount('notifications', 3);

        /* @var $amqpMock DummyProducer */
        $amqpMock = app(ProducerInterface::class);
        $this->assertEquals(3, count($amqpMock->getMessages(ExchangesAndQueuesEnum::COMMUNICATIONS_TOPIC->value)));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(200, $sameResponse->getStatusCode());
    }
}
