<?php

namespace App\Aggregates\Clients;

use App\StorableEvents\Clients\Calendar\CalendarAttendeeAdded;
use App\StorableEvents\Clients\Calendar\CalendarAttendeeDeleted;
use App\StorableEvents\Clients\Calendar\CalendarAttendeeInvited;
use App\StorableEvents\Clients\Calendar\CalendarEventCreated;
use App\StorableEvents\Clients\Calendar\CalendarEventDeleted;
use App\StorableEvents\Clients\Calendar\CalendarEventRestored;
use App\StorableEvents\Clients\Calendar\CalendarEventTrashed;
use App\StorableEvents\Clients\Calendar\CalendarEventTypes\CalendarEventTypeCreated;
use App\StorableEvents\Clients\Calendar\CalendarEventTypes\CalendarEventTypeDeleted;
use App\StorableEvents\Clients\Calendar\CalendarEventTypes\CalendarEventTypeRestored;
use App\StorableEvents\Clients\Calendar\CalendarEventTypes\CalendarEventTypeTrashed;
use App\StorableEvents\Clients\Calendar\CalendarEventTypes\CalendarEventTypeUpdated;
use App\StorableEvents\Clients\Calendar\CalendarEventUpdated;

use App\StorableEvents\Clients\Calendar\CalendarInviteAccepted;
use App\StorableEvents\Clients\Calendar\CalendarInviteDeclined;
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
        $this->recordThat(new CalendarEventTrashed($this->uuid(), $trashed_by_user_id, $id));

        return $this;
    }

    public function restoreCalendarEvent(string $restored_by_user_id, string $id)
    {
        $this->recordThat(new CalendarEventRestored($this->uuid(), $restored_by_user_id, $id));

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

    public function addCalendarAttendee(string $updated_by_user_id, array $payload)
    {
        $this->recordThat(new CalendarAttendeeAdded($this->uuid(), $updated_by_user_id, $payload));

        return $this;
    }

    public function deleteCalendarAttendee(string $updated_by_user_id, array $payload)
    {
        $this->recordThat(new CalendarAttendeeDeleted($this->uuid(), $updated_by_user_id, $payload));

        return $this;
    }

    public function inviteCalendarAttendee(string $updated_by_user_id, array $payload)
    {
        $this->recordThat(new CalendarAttendeeInvited($this->uuid(), $updated_by_user_id, $payload));

        return $this;
    }

    public function acceptCalendarEvent(string $updated_by_user_id, array $payload)
    {
        $this->recordThat(new CalendarInviteAccepted($this->uuid(), $updated_by_user_id, $payload));

        return $this;
    }

    public function declineCalendarEvent(string $updated_by_user_id, array $payload)
    {
        $this->recordThat(new CalendarInviteDeclined($this->uuid(), $updated_by_user_id, $payload));

        return $this;
    }
}
