<?php

namespace Feature\Communications;

use App\Features\Communications\Domain\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function testUnauthorizedGetNotifications()
    {
        $response = $this->json(
            'GET',
            'server-api/notifications/by-identity/',
        );

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testValidationGetNotifications()
    {
        $this->iAmAuthenticatedServerApiUser();
        $response = $this->json(
            'GET',
            'server-api/notifications/by-identity/',
        );

        $this->assertEquals(422, $response->getStatusCode());
    }

    public function testGetEmptyNotifications()
    {
        $this->iAmAuthenticatedServerApiUser();
        $response = $this->json(
            'GET',
            'server-api/notifications/by-identity/',
            [
                'identity' => '+79493752333',
            ],
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([], $response->json()['notifications']);
    }

    public function testGetNotEmptyNotifications()
    {
        $notification = Notification::factory()->create();

        $this->iAmAuthenticatedServerApiUser();
        $response = $this->json(
            'GET',
            'server-api/notifications/by-identity/',
            [
                'identity' => $notification->getIdentity(),
            ],
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(1, $response->json()['notifications']);
    }
}
