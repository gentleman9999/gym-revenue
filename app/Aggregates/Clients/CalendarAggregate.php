<?php

namespace App\Aggregates\Clients;


use App\StorableEvents\Clients\Calendar\CalendarEventCreated;
use App\StorableEvents\Clients\Calendar\CalendarEventTypeCreated;
use App\StorableEvents\Clients\Calendar\CalendarEventDeleted;
use App\StorableEvents\Clients\Calendar\CalendarEventUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class CalendarAggregate extends AggregateRoot
{

    public function createCalendarEvent(string $created_by_user_id, array $payload)
    {
        $this->recordThat(new CalendarEventCreated($this->uuid(), $created_by_user_id, $payload));
        return $this;
    }

    public function updateCalendarEvent(string $updated_by_user_id, array $payload)
    {
        $this->recordThat(new CalendarEventUpdated($this->uuid(), $updated_by_user_id, $payload));
        return $this;
    }

    public function deleteCalendarEvent(string $deleted_by_user_id, string $id)
    {
        $this->recordThat(new CalendarEventDeleted($this->uuid(), $deleted_by_user_id, $id));
        return $this;
    }

    public function trashCalendarEvent(string $trashed_by_user_id, string $id)
    {
        $this->recordThat(new CalendarEventDeleted($this->uuid(), $trashed_by_user_id, $id));
        return $this;
    }

    public function restoreCalendarEvent(string $restored_by_user_id, string $id)
    {
        $this->recordThat(new CalendarEventDeleted($this->uuid(), $restored_by_user_id, $id));
        return $this;
    }

    public function createCalendarEventType(string $created_by_user_id, array $payload)
    {
        $this->recordThat(new CalendarEventTypeCreated($this->uuid(), $created_by_user_id, $payload));
        return $this;
    }

}
