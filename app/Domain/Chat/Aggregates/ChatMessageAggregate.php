<?php

declare(strict_types=1);

namespace App\Domain\Chat\Aggregates;

use App\Domain\Chat\Events\MessageCreated;
use App\Domain\Chat\Events\MessageDeleted;
use App\Domain\Chat\Events\MessageRestored;
use App\Domain\Chat\Events\MessageUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class ChatMessageAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new MessageCreated($payload));

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new MessageUpdated($payload));

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new MessageDeleted());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new MessageRestored());

        return $this;
    }
}
