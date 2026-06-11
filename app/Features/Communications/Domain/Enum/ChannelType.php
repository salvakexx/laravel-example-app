<?php

declare(strict_types=1);

namespace App\Features\Communications\Domain\Enum;

enum ChannelType: string
{
    case SMS = 'sms';
    case EMAIL = 'email';
}
