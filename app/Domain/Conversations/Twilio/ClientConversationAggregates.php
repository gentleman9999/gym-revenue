<?php

declare(strict_types=1);

namespace App\Domain\Conversations\Twilio;

use App\Domain\Conversations\Twilio\Events\ClientConversationCreated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class ClientConversationAggregates extends AggregateRoot
{
    public function clientConversationCreated(array $payload): static
    {
        $this->recordThat(new ClientConversationCreated($payload));

        return $this;
    }
}
