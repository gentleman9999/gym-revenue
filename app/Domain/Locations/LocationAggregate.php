<?php

namespace App\Domain\Locations;

use App\Domain\Locations\Events\LocationCreated;
use App\Domain\Locations\Events\LocationDeleted;
use App\Domain\Locations\Events\LocationRestored;
use App\Domain\Locations\Events\LocationTrashed;
use App\Domain\Locations\Events\LocationUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class LocationAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new LocationCreated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new LocationTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new LocationRestored());

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
