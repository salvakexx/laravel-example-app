<?php

declare(strict_types=1);

namespace App\Features\Communications\Infrastructure\Port\Http\ServerApi\SendNotificationBulk;

use App\Features\Communications\Application\Service\NotificationService;
use App\Http\Controllers\Controller as BaseController;
use App\Idempotency\IdempotencyService;
use Illuminate\Http\JsonResponse;

final class SendNotificationBulkController extends BaseController
{
    public function __construct(
        private NotificationService $notificationService,
        private IdempotencyService $idempotencyService
    ) {
    }

    public function send(SendNotificationBulkRequest $request): JsonResponse
    {
        $isCachedRequest = $this->idempotencyService->isIdempotencyCacheExistByApiFormRequest($request);

        if ($isCachedRequest === false) {
            $this->idempotencyService->persistIdempotencyCacheByApiFormRequest($request);

            $this->notificationService->sendBulk(
                channel: $request->input('channel'),
                message: $request->input('message'),
                recipients: array_unique($request->input('recipients')),
            );
        }

        return response()->json(['status' => true]);
    }
}
