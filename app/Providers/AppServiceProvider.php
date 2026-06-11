<?php

namespace App\Providers;

use App\Console\Commands\RabbitMqDeclareAllCommand;
use App\Integrations\EmailGateway\SendEmail\SendEmailProvider;
use App\Integrations\EmailGateway\SendEmail\SendEmailProviderInterface;
use App\Integrations\SmsGateway\SendSms\SendSmsProvider;
use App\Integrations\SmsGateway\SendSms\SendSmsProviderInterface;
use App\Queue\AmqpProducer;
use App\Queue\ProducerInterface;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->commands([
            RabbitMqDeclareAllCommand::class,
        ]);

        $this->app->bind(ProducerInterface::class, AmqpProducer::class);
        $this->app->bind(SendSmsProviderInterface::class, SendSmsProvider::class);
        $this->app->bind(SendEmailProviderInterface::class, SendEmailProvider::class);
        Scramble::configure()->routes(function (Route $route) {
            return Str::startsWith($route->uri, 'server-api/');
        });
        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('basic')
                );
            });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
