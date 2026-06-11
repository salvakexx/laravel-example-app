<?php

namespace App\Features\Communications\Domain\Models\Factories;

use App\Features\Communications\Domain\Enum\ChannelType;
use App\Features\Communications\Domain\Enum\NotificationStatus;
use App\Features\Communications\Domain\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'identity' => $this->faker->e164PhoneNumber(),
            'channel' => ChannelType::SMS->value,
            'message' => $this->faker->sentence(),
            'sender' => $this->faker->sentence(),
            'status' => NotificationStatus::QUEUED->value,
        ];
    }
}
