<?php

declare(strict_types=1);

namespace App\Queue;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Queue;

class AmqpProducer implements ProducerInterface
{
    public function publish(string $exchange, ShouldQueue $job, ?string $routingKey = null, ?int $priority = null): void
    {
        $options = [
            'exchange' => $exchange,
            'exchange_type' => config('exchanges.'.$exchange.'.exchange_type'),
        ];
        if ($priority !== null) {
            $options['priority'] = $priority;
        }
        /* @var QueueWorker $queueConnection */
        $queueConnection = Queue::connection('rabbitmq');
        $queueConnection->pushWithOptions($job, '', null, $options);
    }
}
