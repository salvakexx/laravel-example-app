<?php

namespace App\Queue;

use Illuminate\Contracts\Queue\ShouldQueue;

interface ProducerInterface
{
    public function publish(string $exchange, ShouldQueue $job, ?string $routingKey = null, ?int $priority = null): void;
}
