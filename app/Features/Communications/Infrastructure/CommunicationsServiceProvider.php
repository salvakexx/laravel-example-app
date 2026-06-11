<?php

declare(strict_types=1);

namespace App\Features\Communications\Infrastructure;

use App\Features\Communications\Domain\Storage\NotificationStorageInterface;
use App\Features\Communications\Infrastructure\Storage\NotificationStorage;
use Illuminate\Support\ServiceProvider;

class CommunicationsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(NotificationStorageInterface::class, NotificationStorage::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/Routes/Routes.php');
    }
}
