<?php

declare(strict_types=1);

namespace App\Domain\Chat\Aggregates;

use App\Domain\Chat\Events\ChatParticipantCreated;
use App\Domain\Chat\Events\ChatParticipantDeleted;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class ChatParticipantAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new ChatParticipantCreated($payload));

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new ChatParticipantDeleted());

        return $this;
    }
}
