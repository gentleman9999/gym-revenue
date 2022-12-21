<?php

namespace App\Domain\CalendarEvents;

use App\Domain\CalendarEvents\Events\CalendarEventCreated;
use App\Domain\CalendarEvents\Events\CalendarEventDeleted;
use App\Domain\CalendarEvents\Events\CalendarEventFileUploaded;
use App\Domain\CalendarEvents\Events\CalendarEventNotified;
use App\Domain\CalendarEvents\Events\CalendarEventRestored;
use App\Domain\CalendarEvents\Events\CalendarEventTrashed;
use App\Domain\CalendarEvents\Events\CalendarEventUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class CalendarEventAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new CalendarEventCreated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new CalendarEventTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new CalendarEventRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new CalendarEventDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new CalendarEventUpdated($payload));

        return $this;
    }

    public function notify(array $payload): static
    {
        $this->recordThat(new CalendarEventNotified($payload));

        return $this;
    }

    public function upload(array $payload): static
    {
        $this->recordThat(new CalendarEventFileUploaded($payload));

        return $this;
    }
}
