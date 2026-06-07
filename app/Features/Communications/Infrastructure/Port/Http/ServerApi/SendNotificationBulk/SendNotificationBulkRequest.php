<?php

declare(strict_types=1);

namespace App\Features\Communications\Infrastructure\Port\Http\ServerApi\SendNotificationBulk;

use App\Features\Communications\Domain\Enum\ChannelType;
use App\Http\Requests\ApiFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class
SendNotificationBulkRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'channel' => ['required', Rule::enum(ChannelType::class)],
            'message' => 'required|string',
            'recipients' => 'required|array',
            'recipients.*' => $this->getRecipientsValidationRuleByChannel(),
        ];
    }

    private function getRecipientsValidationRuleByChannel(): ?string
    {
        switch ($this->input('channel')) {
            case ChannelType::SMS->value:
                return 'phone:INTERNATIONAL';
            case ChannelType::EMAIL->value:
                return 'email';
            default:
                return null;
        }
    }
}
