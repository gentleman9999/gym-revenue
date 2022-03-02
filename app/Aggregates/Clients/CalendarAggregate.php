<?php

namespace App\Aggregates\Clients;


use App\StorableEvents\Clients\Calendar\CalendarEventCreated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class CalendarAggregate extends AggregateRoot
{

    public function createCalendarEvent(string $title, $start, $end)
    {
        $this->recordThat(new CalendarEventCreated($this->uuid(), $title, $start, $end));
        return $this;
    }

    public function trashCalendarEvent(array $event)
    {
        return $this;
    }

    public function restoreCalendarEvent(array $event)
    {
        return $this;
    }

    public function deleteCalendarEvent(array $event)
    {
        return $this;
    }

    public function renameCalendarEvent(array $event)
    {
        return $this;
    }
}
