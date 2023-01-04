<?php

declare(strict_types=1);

namespace App\Domain\LocationVendors;

use App\Domain\LocationVendors\Events\LocationVendorCreated;
use App\Domain\LocationVendors\Events\LocationVendorDeleted;
use App\Domain\LocationVendors\Events\LocationVendorRestored;
use App\Domain\LocationVendors\Events\LocationVendorTrashed;
use App\Domain\LocationVendors\Events\LocationVendorUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class LocationVendorAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new LocationVendorCreated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new LocationVendorTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new LocationVendorRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new LocationVendorDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new LocationVendorUpdated($payload));

        return $this;
    }
}
