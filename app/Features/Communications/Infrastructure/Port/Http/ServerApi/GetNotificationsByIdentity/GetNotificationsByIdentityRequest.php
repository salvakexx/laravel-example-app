<?php

declare(strict_types=1);

namespace App\Features\Communications\Infrastructure\Port\Http\ServerApi\GetNotificationsByIdentity;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class GetNotificationsByIdentityRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'identity' => ['required', 'string', $this->getIdentityValidationRuleByInput()],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
            'offset' => ['nullable', 'integer', 'min:0'],
        ];
    }

    private function getIdentityValidationRuleByInput(): string
    {
        if ($this->query('identity') && str_contains($this->query('identity'), '@')) {
            return 'email';
        }

        return 'phone:INTERNATIONAL';
    }
}
