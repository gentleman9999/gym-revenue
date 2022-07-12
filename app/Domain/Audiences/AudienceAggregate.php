<?php

namespace App\Domain\Audiences;

use App\Domain\Audiences\Events\AudienceCreated;
use App\Domain\Audiences\Events\AudienceDeleted;
use App\Domain\Audiences\Events\AudienceRestored;
use App\Domain\Audiences\Events\AudienceTrashed;
use App\Domain\Audiences\Events\AudienceUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class AudienceAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new AudienceCreated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new AudienceTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new AudienceRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new AudienceDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new AudienceUpdated($payload));

        return $this;
    }
}
