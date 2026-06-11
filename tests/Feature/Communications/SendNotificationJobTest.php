<?php

namespace Feature\Communications;

use App\Features\Communications\Application\Jobs\SendNotificationJob;
use App\Features\Communications\Application\Service\NotificationSendService;
use App\Features\Communications\Application\Service\NotificationService;
use App\Features\Communications\Domain\Enum\ChannelType;
use App\Features\Communications\Domain\Enum\NotificationStatus;
use App\Features\Communications\Domain\Models\Notification;
use App\Integrations\EmailGateway\SendEmail\DummySendEmailProvider;
use App\Integrations\EmailGateway\SendEmail\EmailDoesNotExistException;
use App\Integrations\EmailGateway\SendEmail\SendEmailProviderInterface;
use App\Integrations\SmsGateway\SendSms\DummySendSmsProvider;
use App\Integrations\SmsGateway\SendSms\PhoneDoesNotExistException;
use App\Integrations\SmsGateway\SendSms\SendSmsProviderInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SendNotificationJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_sms_send_notification_job()
    {
        $notification = Notification::factory()->create([
            'channel' => ChannelType::SMS->value,
        ]);

        $job = new SendNotificationJob($notification);

        $this->mockEmailSendService();
        $this->mockSmsSendService();

        $job->handle(app(NotificationSendService::class), app(NotificationService::class));

        $this->assertEquals(NotificationStatus::DELIVERED->value, $notification->getStatus());
        $this->assertEquals(ChannelType::SMS->value, $notification->getChannel());
    }

    public function test_valid_email_send_notification_job()
    {
        $notification = Notification::factory()->create([
            'channel' => ChannelType::EMAIL->value,
        ]);

        $job = new SendNotificationJob($notification);

        $this->mockEmailSendService();
        $this->mockSmsSendService();

        $job->handle(app(NotificationSendService::class), app(NotificationService::class));

        $this->assertEquals(NotificationStatus::DELIVERED->value, $notification->getStatus());
        $this->assertEquals(ChannelType::EMAIL->value, $notification->getChannel());
    }

    public function test_not_valid_email_send_notification_job()
    {
        $notification = Notification::factory()->create([
            'channel' => ChannelType::EMAIL->value,
        ]);

        $job = new SendNotificationJob($notification);

        $mock = \Mockery::mock(DummySendEmailProvider::class);
        $mock->shouldReceive('send')->once()->andThrow(new EmailDoesNotExistException);

        $this->instance(SendEmailProviderInterface::class, $mock);

        $job->handle(app(NotificationSendService::class), app(NotificationService::class));

        $this->assertEquals(NotificationStatus::FAILED->value, $notification->getStatus());
        $this->assertEquals(ChannelType::EMAIL->value, $notification->getChannel());
    }

    public function test_not_valid_sms_send_notification_job()
    {
        $notification = Notification::factory()->create([
            'channel' => ChannelType::SMS->value,
        ]);

        $job = new SendNotificationJob($notification);

        $mock = \Mockery::mock(DummySendSmsProvider::class);
        $mock->shouldReceive('send')->once()->andThrow(new PhoneDoesNotExistException);

        $this->instance(SendSmsProviderInterface::class, $mock);

        $job->handle(app(NotificationSendService::class), app(NotificationService::class));

        $this->assertEquals(NotificationStatus::FAILED->value, $notification->getStatus());
        $this->assertEquals(ChannelType::SMS->value, $notification->getChannel());
    }
}
