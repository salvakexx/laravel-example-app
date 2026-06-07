<?php

use \App\Queue\ExchangesAndQueuesEnum;

return [
    ExchangesAndQueuesEnum::COMMUNICATIONS_TOPIC->value => [
        'type' => ExchangesAndQueuesEnum::TOPIC->value,
        'queues' => [
            [
                'name' => ExchangesAndQueuesEnum::COMMUNICATIONS_QUEUE->value,
                'x-max-priority' => 10,
            ]
        ]
    ],
];
