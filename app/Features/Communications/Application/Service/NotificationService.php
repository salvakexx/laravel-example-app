<?php

declare(strict_types=1);

namespace App\Features\Communications\Application\Service;

use App\Features\Communications\Application\Jobs\SendNotificationJob;
use App\Features\Communications\Domain\Enum\ChannelType;
use App\Features\Communications\Domain\Enum\NotificationStatus;
use App\Features\Communications\Domain\Models\Notification;
use App\Features\Communications\Domain\Storage\NotificationStorageInterface;
use App\Queue\ExchangesAndQueuesEnum;
use App\Queue\ProducerInterface;
use Illuminate\Support\Facades\DB;
use Propaganistas\LaravelPhone\PhoneNumber;

class NotificationService
{
    public function __construct(
        private readonly NotificationStorageInterface $notificationStorage,
        private readonly ProducerInterface $producer,
    ) {}

    /**
     * @param string[] $recipients
     */
    public function sendBulk(string $channel, string $message, array $recipients): void
    {
        foreach ($recipients as $identity) {
            DB::transaction(function () use ($channel, $message, $identity) {
                $createdNotification = $this->notificationStorage->saveNotification(
                    channel: $channel,
                    message: $message,
                    identity: $this->prepareIdentityByChannel($channel, $identity),
                );

                $this->producer->publish(
                    ExchangesAndQueuesEnum::COMMUNICATIONS_TOPIC->value,
                    new SendNotificationJob($createdNotification),
                    null,
                    $this->preparePriorityByChannel($channel)
                );
            });
        }
    }

    public function changeStatus(Notification $notification, NotificationStatus $notificationStatus): void
    {
        $notification->setStatus($notificationStatus)->save();
    }

    private function prepareIdentityByChannel(string $channel, string $identity): string
    {
        $channelType = ChannelType::from($channel);

        switch ($channelType) {
            case ChannelType::SMS:
                return (string) (new PhoneNumber($identity));
            default:
                return $identity;
        }
    }

    private function preparePriorityByChannel(string $channel): int
    {
        $channelType = ChannelType::from($channel);

        switch ($channelType) {
            case ChannelType::SMS:
                return 2;
            default:
                return 1;
        }
    }
}
