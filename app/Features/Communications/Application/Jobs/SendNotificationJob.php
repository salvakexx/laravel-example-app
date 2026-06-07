<?php

declare(strict_types=1);

namespace App\Features\Communications\Application\Jobs;

use App\Features\Communications\Application\Service\NotificationSendService;
use App\Features\Communications\Application\Service\NotificationService;
use App\Features\Communications\Domain\Enum\NotificationStatus;
use App\Features\Communications\Domain\Models\Notification;
use App\Integrations\EmailGateway\SendEmail\EmailDoesNotExistException;
use App\Integrations\SmsGateway\SendSms\PhoneDoesNotExistException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Tries;

/**
Под капотом laravel использует уникальные идентификаторы событий для дедупликации повторов,
так что при выставлении Tries в 0 возможно добиться effectively-once
 */
#[Backoff(30)]
#[Tries(10)]
class SendNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Notification $notification,
    ) {
    }

    public function handle(
        NotificationSendService $notificationSendService,
        NotificationService $notificationService,
    ): void {
        try {
            $notificationSendService->sendNotification($this->notification);
            $notificationService->changeStatus($this->notification, NotificationStatus::DELIVERED);
        } catch (EmailDoesNotExistException|PhoneDoesNotExistException) {
            $notificationService->changeStatus($this->notification, NotificationStatus::FAILED);
        }
    }
}
