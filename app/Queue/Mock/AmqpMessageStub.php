<?php

declare(strict_types=1);

namespace App\Queue\Mock;

use Illuminate\Contracts\Queue\ShouldQueue;

class AmqpMessageStub
{
    /** @var ShouldQueue */
    private $msgBody;

    /** @var string */
    private $topic;

    /** @var string|null */
    private $routingKey;

    /** @var mixed[]|null */
    private $additionalProperties;

    /**
     * @param mixed[]|null $additionalProperties
     */
    public function __construct(ShouldQueue $msgBody, string $topic, ?string $routingKey = null, ?array $additionalProperties = [])
    {
        $this->msgBody = $msgBody;
        $this->topic = $topic;
        $this->routingKey = $routingKey;
        $this->additionalProperties = $additionalProperties;
    }

    public function getBody(): ShouldQueue
    {
        return $this->msgBody;
    }

    public function hasProperty(string $propertyKey): bool
    {
        return array_key_exists($propertyKey, (array)$this->additionalProperties);
    }
    public function getTopic(): string
    {
        return $this->topic;
    }

    public function getRoutingKey(): ?string
    {
        return $this->routingKey;
    }
}
