<?php

declare(strict_types=1);

namespace App\Features\Communications\Domain\Storage;

use App\Features\Communications\Domain\Models\Notification;

interface NotificationStorageInterface
{
    public function saveNotification(string $channel, string $message, string $identity): Notification;
}
