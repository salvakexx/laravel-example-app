<?php

declare(strict_types=1);

namespace App\Queue;

use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\RabbitMQQueue;

class QueueWorker extends RabbitMQQueue
{
    public function pushWithOptions($job, $data = '', $queue = null, $options = [])
    {
        return $this->enqueueUsing(
            $job,
            $this->createPayload($job, $this->getQueue($queue), $data, $job->delay),
            $queue,
            $job->delay,
            function ($payload, $queue) use ($options) {
                return $this->pushRaw($payload, $queue, $options);
            }
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws AMQPProtocolChannelException
     */
    public function pushRaw($payload, $queue = null, array $options = []): int|string|null
    {
        [$destination, $exchange, $exchangeType, $attempts, $priority] = $this->publishProperties($queue, $options);

        $this->declareDestination($destination, $exchange, $exchangeType);

        [$message, $correlationId] = $this->createMessage($payload, $attempts, $priority);

        $this->publishBasic($message, $exchange, $destination, true);

        return $correlationId;
    }

    /**
     * Determine all publish properties.
     */
    protected function publishProperties($queue, array $options = []): array
    {
        $queue = $this->getQueue($queue);
        $attempts = Arr::get($options, 'attempts') ?: 0;

        $destination = $this->getRoutingKey($queue);
        $exchange = $this->getExchange(Arr::get($options, 'exchange'));
        $exchangeType = $this->getExchangeType(Arr::get($options, 'exchange_type'));
        $priority = Arr::get($options, 'priority') ?? null;

        return [$destination, $exchange, $exchangeType, $attempts, $priority];
    }

    /**
     * Create a AMQP message.
     */
    protected function createMessage($payload, int $attempts = 0, ?int $priority = null): array
    {
        $properties = [
            'content_type' => 'application/json',
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
        ];

        $currentPayload = json_decode($payload, true);
        if ($correlationId = $currentPayload['id'] ?? null) {
            $properties['correlation_id'] = $correlationId;
        }

        if ($this->getRabbitMQConfig()->isPrioritizeDelayed()) {
            $properties['priority'] = $attempts;
        }

        if (isset($currentPayload['data']['command'])) {
            // If the command data is encrypted, decrypt it first before attempting to unserialize
            if (is_subclass_of($currentPayload['data']['commandName'], ShouldBeEncrypted::class)) {
                $currentPayload['data']['command'] = Crypt::decrypt($currentPayload['data']['command']);
            }

            $commandData = unserialize($currentPayload['data']['command']);
            if (property_exists($commandData, 'priority')) {
                $properties['priority'] = $commandData->priority;
            }
        }

        if ($priority !== null) {
            $properties['priority'] = $priority;
        }

        $message = new AMQPMessage($payload, $properties);

        $message->set('application_headers', new AMQPTable([
            'laravel' => [
                'attempts' => $attempts,
            ],
        ]));

        return [
            $message,
            $correlationId,
        ];
    }
}
