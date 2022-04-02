<?php

namespace App\Aggregates\Clients;


use App\StorableEvents\Clients\Calendar\CalendarEventTypes\CalendarEventCreated;
use App\StorableEvents\Clients\Calendar\CalendarEventTypes\CalendarEventDeleted;
use App\StorableEvents\Clients\Calendar\CalendarEventTypes\CalendarEventTypeCreated;
use App\StorableEvents\Clients\Calendar\CalendarEventTypes\CalendarEventTypeDeleted;
use App\StorableEvents\Clients\Calendar\CalendarEventTypes\CalendarEventTypeRestored;
use App\StorableEvents\Clients\Calendar\CalendarEventTypes\CalendarEventTypeTrashed;
use App\StorableEvents\Clients\Calendar\CalendarEventTypes\CalendarEventTypeUpdated;
use App\StorableEvents\Clients\Calendar\CalendarEventTypes\CalendarEventUpdated;
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

    public function updateCalendarEventType(string $updated_by_user_id, array $payload)
    {
        $this->recordThat(new CalendarEventTypeUpdated($this->uuid(), $updated_by_user_id, $payload));
        return $this;
    }

    public function trashCalendarEventType(string $trashed_by_user_id, string $id)
    {
        $this->recordThat(new CalendarEventTypeTrashed($this->uuid(), $trashed_by_user_id, $id));
        return $this;
    }

    public function restoreCalendarEventType(string $trashed_by_user_id, string $id)
    {
        $this->recordThat(new CalendarEventTypeRestored($this->uuid(), $trashed_by_user_id, $id));
        return $this;
    }

    public function deleteCalendarEventType(string $trashed_by_user_id, string $id)
    {
        $this->recordThat(new CalendarEventTypeDeleted($this->uuid(), $trashed_by_user_id, $id));
        return $this;
    }
}
