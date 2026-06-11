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

    /**
     * @return AmqpMessageStub[]
     */
    public function getMessages(string $topic): array
    {
        return $this->publishedMessages[$topic] ?? [];
    }

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void {}

    public function setContentType(string $contentType): void {}

    public function close(): void {}
}
