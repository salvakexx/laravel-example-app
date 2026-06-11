<?php

declare(strict_types=1);

namespace App\Features\Communications\Infrastructure\Storage;

use App\Features\Communications\Domain\Enum\NotificationStatus;
use App\Features\Communications\Domain\Models\Notification;
use App\Features\Communications\Domain\Storage\NotificationStorageInterface;

class NotificationStorage implements NotificationStorageInterface
{
    public function saveNotification(string $channel, string $message, string $identity): Notification
    {
        $notification = (new Notification)
            ->setChannel($channel)
            ->setMessage($message)
            ->setIdentity($identity)
            ->setStatus(NotificationStatus::QUEUED)
            ->setSender('default');

        $notification->save();

        return $notification;
    }
}
