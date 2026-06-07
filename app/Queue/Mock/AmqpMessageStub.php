<?php

declare(strict_types=1);

namespace App\Queue\Mock;

class AmqpMessageStub
{
    private $msgBody;

    /** @var string */
    private $topic;

    /** @var string */
    private $routingKey;

    /** @var array */
    private $additionalProperties;

    public function __construct($msgBody, string $topic, ?string $routingKey = null, ?array $additionalProperties = [])
    {
        $this->msgBody = $msgBody;
        $this->topic = $topic;
        $this->routingKey = $routingKey;
        $this->additionalProperties = $additionalProperties;
    }

    public function getBody()
    {
        return $this->msgBody;
    }

    public function getBodyAsJson(): array
    {
        return json_decode($this->msgBody, true);
    }

    public function hasJsonBody(array $expectedData): bool
    {
        return $this->arrayEquals($this->getBodyAsJson(), $expectedData);
    }

    public function hasRoutingKey(string $expectedRoutingKey): bool
    {
        return $this->routingKey === $expectedRoutingKey;
    }

    public function hasProperty(string $propertyKey): bool
    {
        return array_key_exists($propertyKey, $this->additionalProperties);
    }

    private function arrayEquals(array $array1, array $array2): bool
    {
        if (count($array1) !== count($array2)) {
            return false;
        }

        foreach ($array1 as $key => $value) {
            if (!array_key_exists($key, $array2)) {
                return false;
            }
            if ($value !== $array2[$key]) {
                return false;
            }
        }

        return true;
    }
}
