<?php

declare(strict_types=1);

namespace App\Features\Communications\Infrastructure\Port\Http\ServerApi\GetNotificationsByIdentity;

use App\Features\Communications\Domain\Models\Notification;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\JsonResponse;

final class GetNotificationsByIdentityController extends BaseController
{
    public function get(GetNotificationsByIdentityRequest $request): JsonResponse
    {
        $identity = $request->input('identity');
        $limit = $request->input('limit', 50);
        $offset = $request->input('offset', 0);

        $total = Notification::forIdentity($identity)->count();

        $notificationsData = Notification::forIdentity($identity)
            ->orderedByCreated()
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->map(fn (Notification $notification) => [
                'id' => (string) $notification->getKey(),
                'identity' => $notification->getIdentity(),
                'message' => $notification->getMessage(),
                'channel' => $notification->getChannel(),
                'status' => $notification->getStatus(),
                'sender' => $notification->getSender(),
                'created_at' => $notification->created_at?->toIso8601String(),
            ])
            ->toArray();

        return response()->json([
            'notifications' => $notificationsData,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }
}
