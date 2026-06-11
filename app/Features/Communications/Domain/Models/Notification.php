<?php

declare(strict_types=1);

namespace App\Features\Communications\Domain\Models;

use App\Features\Communications\Domain\Enum\NotificationStatus;
use App\Features\Communications\Domain\Models\Factories\NotificationFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/* @property string $identity */
/* @property string $message */
/* @property string $channel */
/* @property string $status */
/* @property string $sender */
#[Table('notifications', key: 'id')]
#[UseFactory(NotificationFactory::class)]
class Notification extends Model
{
    use HasFactory;

    public function getIdentity(): string
    {
        return $this->identity;
    }

    public function setIdentity(string $identity): self
    {
        $this->identity = $identity;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getChannel(): string
    {
        return $this->channel;
    }

    public function setChannel(string $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(NotificationStatus $status): self
    {
        $this->status = $status->value;

        return $this;
    }

    public function getSender(): string
    {
        return $this->sender;
    }

    public function setSender(string $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    #[Scope]
    protected function scopeForIdentity(Builder $query, string $identity): void
    {
        $query->where('identity', $identity);
    }

    #[Scope]
    public function scopeOrderedByCreated(Builder $query): void
    {
        $query->orderBy('created_at', 'desc');
    }
}
