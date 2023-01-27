<?php

declare(strict_types=1);

namespace App\Domain\GymAmenities;

use App\Domain\GymAmenities\Events\GymAmenityCreated;
use App\Domain\GymAmenities\Events\GymAmenityDeleted;
use App\Domain\GymAmenities\Events\GymAmenityRestored;
use App\Domain\GymAmenities\Events\GymAmenityTrashed;
use App\Domain\GymAmenities\Events\GymAmenityUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class GymAmenityAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new GymAmenityCreated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new GymAmenityTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new GymAmenityRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new GymAmenityDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new GymAmenityUpdated($payload));

        return $this;
    }
}
