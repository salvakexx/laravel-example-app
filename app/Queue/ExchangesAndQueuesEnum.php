<?php

declare(strict_types=1);

namespace App\Queue;

enum ExchangesAndQueuesEnum: string
{
    case TOPIC = 'topic';

    case COMMUNICATIONS_TOPIC = 'event.communications.send';

    case COMMUNICATIONS_QUEUE = 'queue.event.communications.send';
}
