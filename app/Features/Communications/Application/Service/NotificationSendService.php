<?php

declare(strict_types=1);

namespace App\Features\Communications\Application\Service;

use App\Features\Communications\Domain\Enum\ChannelType;
use App\Features\Communications\Domain\Models\Notification;
use App\Integrations\EmailGateway\SendEmail\SendEmailProviderInterface;
use App\Integrations\SmsGateway\SendSms\SendSmsProviderInterface;

class NotificationSendService
{
    public function __construct(
        private readonly SendSmsProviderInterface $sendSmsProvider,
        private readonly SendEmailProviderInterface $sendEmailProvider,
    ) {
    }

    public function sendNotification(Notification $notification): void
    {
        $channelType = ChannelType::from($notification->getChannel());

        switch ($channelType) {
            case ChannelType::SMS:
                $this->sendSmsProvider->send($notification->getIdentity(), $notification->getMessage());
            case ChannelType::EMAIL:
                $this->sendEmailProvider->send($notification->getIdentity(), 'Notification', $notification->getMessage());
        }
    }
}
