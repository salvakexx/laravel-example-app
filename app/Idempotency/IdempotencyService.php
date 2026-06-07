<?php

declare(strict_types=1);

namespace App\Idempotency;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Support\Facades\Cache;

class IdempotencyService
{
    public function persistIdempotencyCacheByApiFormRequest(ApiFormRequest $request): void
    {
        Cache::put($this->generateIdempotencyHashByApiFormRequest($request), true, 3600);
    }

    public function isIdempotencyCacheExistByApiFormRequest(ApiFormRequest $request): bool
    {
        return Cache::get($this->generateIdempotencyHashByApiFormRequest($request), false) === true;
    }

    private function generateIdempotencyHashByApiFormRequest(ApiFormRequest $request): string
    {
        return hash('sha256', $request->path().json_encode($request->array()));
    }
}
