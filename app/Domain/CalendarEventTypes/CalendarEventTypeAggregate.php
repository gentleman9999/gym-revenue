<?php

namespace App\Domain\CalendarEventTypes;

use App\Domain\CalendarEventTypes\Events\CalendarEventTypeCreated;
use App\Domain\CalendarEventTypes\Events\CalendarEventTypeDeleted;
use App\Domain\CalendarEventTypes\Events\CalendarEventTypeRestored;
use App\Domain\CalendarEventTypes\Events\CalendarEventTypeTrashed;
use App\Domain\CalendarEventTypes\Events\CalendarEventTypeUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class CalendarEventTypeAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new CalendarEventTypeCreated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new CalendarEventTypeTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new CalendarEventTypeRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new CalendarEventTypeDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new CalendarEventTypeUpdated($payload));

        return $this;
    }
}
