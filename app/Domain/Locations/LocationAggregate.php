<?php

namespace App\Domain\Locations;

use App\Domain\Locations\Events\LocationClosed;
use App\Domain\Locations\Events\LocationCreated;
use App\Domain\Locations\Events\LocationDeleted;
use App\Domain\Locations\Events\LocationReopened;
use App\Domain\Locations\Events\LocationUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class LocationAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new LocationCreated($payload));

        return $this;
    }

    public function close(): static
    {
        $this->recordThat(new LocationClosed());

        return $this;
    }

    public function reopen(): static
    {
        $this->recordThat(new LocationReopened());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new LocationDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new LocationUpdated($payload));

        return $this;
    }
}
