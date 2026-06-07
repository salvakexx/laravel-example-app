<?php

declare(strict_types=1);

namespace App\Features\Communications\Domain\Enum;

enum NotificationStatus: string {
    case QUEUED = 'queued';

    case SENDED = 'sended';

    case DELIVERED = 'delivered';

    case FAILED = 'failed';
}
