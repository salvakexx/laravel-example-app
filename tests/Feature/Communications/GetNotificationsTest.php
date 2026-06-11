<?php

namespace Feature\Communications;

use App\Features\Communications\Domain\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthorized_get_notifications()
    {
        $response = $this->json(
            'GET',
            'server-api/notifications/by-identity/',
        );

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_validation_get_notifications()
    {
        $this->iAmAuthenticatedServerApiUser();
        $response = $this->json(
            'GET',
            'server-api/notifications/by-identity/',
        );

        $this->assertEquals(422, $response->getStatusCode());
    }

    public function test_get_empty_notifications()
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

    public function test_get_not_empty_notifications()
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
