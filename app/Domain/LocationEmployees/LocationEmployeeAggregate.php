<?php

declare(strict_types=1);

namespace App\Domain\LocationEmployees;

use App\Domain\LocationEmployees\Events\LocationEmployeeCreated;
use App\Domain\LocationEmployees\Events\LocationEmployeeDeleted;
use App\Domain\LocationEmployees\Events\LocationEmployeeRestored;
use App\Domain\LocationEmployees\Events\LocationEmployeeTrashed;
use App\Domain\LocationEmployees\Events\LocationEmployeeUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class LocationEmployeeAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new LocationEmployeeCreated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new LocationEmployeeTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new LocationEmployeeRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new LocationEmployeeDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new LocationEmployeeUpdated($payload));

        return $this;
    }
}
