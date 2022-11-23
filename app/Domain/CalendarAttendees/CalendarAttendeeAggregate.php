<?php

namespace App\Domain\CalendarAttendees;

use App\Domain\CalendarAttendees\Events\CalendarAttendeeAccepted;
use App\Domain\CalendarAttendees\Events\CalendarAttendeeAdded;
use App\Domain\CalendarAttendees\Events\CalendarAttendeeDeclined;
use App\Domain\CalendarAttendees\Events\CalendarAttendeeDeleted;
use App\Domain\CalendarAttendees\Events\CalendarAttendeeInvited;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class CalendarAttendeeAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new CalendarAttendeeAdded($payload));

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new CalendarAttendeeDeleted());

        return $this;
    }

    public function decline(): static
    {
        $this->recordThat(new CalendarAttendeeDeclined());

        return $this;
    }

    public function accept(): static
    {
        $this->recordThat(new CalendarAttendeeAccepted());

        return $this;
    }

    public function invite(array $payload): static
    {
        $this->recordThat(new CalendarAttendeeInvited($payload));

        return $this;
    }
}
