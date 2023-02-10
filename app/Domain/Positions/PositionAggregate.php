<?php

declare(strict_types=1);

namespace App\Domain\Positions;

use App\Domain\Positions\Events\PositionCreated;
use App\Domain\Positions\Events\PositionDeleted;
use App\Domain\Positions\Events\PositionRestored;
use App\Domain\Positions\Events\PositionTrashed;
use App\Domain\Positions\Events\PositionUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class PositionAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new PositionCreated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new PositionTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new PositionRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new PositionDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new PositionUpdated($payload));

        return $this;
    }
}
