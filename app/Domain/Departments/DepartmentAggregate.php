<?php

namespace App\Domain\Departments;

use App\Domain\Departments\Events\DepartmentCreated;
use App\Domain\Departments\Events\DepartmentDeleted;
use App\Domain\Departments\Events\DepartmentRestored;
use App\Domain\Departments\Events\DepartmentTrashed;
use App\Domain\Departments\Events\DepartmentUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class DepartmentAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new DepartmentCreated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new DepartmentTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new DepartmentRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new DepartmentDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new DepartmentUpdated($payload));

        return $this;
    }
}
