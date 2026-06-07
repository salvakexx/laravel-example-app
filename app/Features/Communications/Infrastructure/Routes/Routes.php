<?php

use App\Features\Communications\Infrastructure\Port\Http\ServerApi\GetNotificationsByIdentity\GetNotificationsByIdentityController;
use App\Features\Communications\Infrastructure\Port\Http\ServerApi\SendNotificationBulk\SendNotificationBulkController;
use Illuminate\Support\Facades\Route;

Route::prefix('server-api')->middleware('api.http_basic_auth')->group(function () {
    Route::post('/send-notifications/bulk/', [SendNotificationBulkController::class, 'send']);
    Route::get('/notifications/by-identity/', [GetNotificationsByIdentityController::class, 'get']);
});
