<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;

class RabbitMqDeclareAllCommand extends Command
{
    protected $signature = 'rabbitmq:declare-all';

    protected $description = 'Declare all exchanges and queues from config/exchanges.php';

    public function handle(): int
    {
        $exchanges = config('exchanges');
        $connection = Queue::connection('rabbitmq');

        // Declare exchanges
        foreach ($exchanges as $exchangeName => $exchangeConfig) {
            $connection->declareExchange($exchangeName, $exchangeConfig['type']);

            $this->info(sprintf('Exchange [%s] declared.', $exchangeName));
            $queues = $exchangeConfig['queues'] ?? [];

            // Declare queues
            foreach ($queues as $queueConfig) {
                $connection->declareQueue($queueConfig['name'], true, false, [
                    'x-max-priority' => $queueConfig['x-max-priority'],
                ]);
                $this->info(sprintf('Queue [%s] declared.', $queueConfig['name']));
                $routingKeys = $queueConfig['routingKeys'] ?? [];
                if (count($routingKeys) > 0) {
                    foreach ($routingKeys as $routingKey) {
                        $connection->bindQueue($queueConfig['name'], $exchangeName, $routingKey);
                        $this->info(sprintf('Queue [%s] binded to exchange [%s] on routingKey [%s].', $queueConfig['name'], $exchangeName, $routingKey));
                    }
                } else {
                    $connection->bindQueue($queueConfig['name'], $exchangeName, '#');
                    $this->info(sprintf('Queue [%s] binded to exchange [%s].', $queueConfig['name'], $exchangeName));
                }
            }
        }

        $this->newLine();
        $this->info('All exchanges and queues have been successfully declared.');

        return self::SUCCESS;
    }
}
