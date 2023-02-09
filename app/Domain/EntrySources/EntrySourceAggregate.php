<?php

declare(strict_types=1);

namespace App\Domain\EntrySources;

use App\Domain\EntrySources\Events\EntrySourceCreated;
use App\Domain\EntrySources\Events\EntrySourceUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class EntrySourceAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new EntrySourceCreated($payload));

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new EntrySourceUpdated($payload));

        return $this;
    }
}
