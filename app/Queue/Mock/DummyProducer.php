<?php

declare(strict_types=1);

namespace App\Queue\Mock;

use App\Queue\ProducerInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DummyProducer implements ProducerInterface
{
    /** @var AmqpMessageStub[][] */
    private $publishedMessages = [];

    public function publish(string $exchange, ShouldQueue $job, ?string $routingKey = null, ?int $priority = null): void
    {
        $properties = [];

        if ($priority !== null) {
            $properties['priority'] = $priority;
        }
        $this->publishedMessages[$exchange][] = new AmqpMessageStub($job, $exchange, $routingKey, $properties);
    }

    public function getMessage(string $topic, int $index): AmqpMessageStub
    {
        return $this->publishedMessages[$topic][$index];
    }

    public function getMessages(string $topic): array
    {
        return $this->publishedMessages[$topic] ?? [];
    }

    public function messagesCount(string $topic): int
    {
        if (true === isset($this->publishedMessages[$topic])) {
            return count($this->publishedMessages[$topic]);
        }

        return 0;
    }

    public function messagesCountInAllQueues(): int
    {
        $count = 0;

        foreach ($this->publishedMessages as $index => $topic) {
            $count += count($this->publishedMessages[$index]);
        }

        return $count;
    }

    public function purgeQueue(string $topic): void
    {
        $this->publishedMessages[$topic] = [];
    }

    /*
     * Next methods implement an interface of the \OldSound\RabbitMqBundle\RabbitMq\Producer class. Sadly RabbitMqBundle
     * doesn't have explicit interface construction of it
     */

    public function setExchangeOptions(array $options = []): void
    {
    }

    public function setQueueOptions(array $options = []): void
    {
    }

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
    }

    public function setDefaultRoutingKey(string $defaultRoutingKey): void
    {
    }

    public function setContentType(string $contentType): void
    {
    }

    public function setDeliveryMode(string $deliveryMode): void
    {
    }

    public function close(): void
    {
    }
}
