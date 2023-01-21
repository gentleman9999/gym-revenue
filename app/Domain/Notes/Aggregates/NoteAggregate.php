<?php

declare(strict_types=1);

namespace App\Domain\Notes\Aggregates;

use App\Domain\Notes\Events\NoteCreated;
use App\Domain\Notes\Events\NoteDeleted;
use App\Domain\Notes\Events\NoteRestored;
use App\Domain\Notes\Events\NoteTrashed;
use App\Domain\Notes\Events\NoteUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class NoteAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new NoteCreated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new NoteTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new NoteRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new NoteDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new NoteUpdated($payload));

        return $this;
    }
}
