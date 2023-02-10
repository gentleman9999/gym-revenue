<?php

declare(strict_types=1);

namespace App\Aggregates\Clients;

use App\Domain\CalendarAttendees\Events\CalendarAttendeeAccepted;
use App\Domain\CalendarAttendees\Events\CalendarAttendeeDeclined;
use App\Domain\CalendarAttendees\Events\CalendarAttendeeDeleted;
use App\Domain\CalendarEvents\Events\CalendarEventCreated;
use App\Domain\CalendarEvents\Events\CalendarEventDeleted;
use App\Domain\CalendarEvents\Events\CalendarEventRestored;
use App\Domain\CalendarEvents\Events\CalendarEventTrashed;
use App\Domain\CalendarEvents\Events\CalendarEventUpdated;
use App\Domain\CalendarEventTypes\Events\CalendarEventTypeCreated;
use App\Domain\CalendarEventTypes\Events\CalendarEventTypeDeleted;
use App\Domain\CalendarEventTypes\Events\CalendarEventTypeRestored;
use App\Domain\CalendarEventTypes\Events\CalendarEventTypeTrashed;
use App\Domain\CalendarEventTypes\Events\CalendarEventTypeUpdated;
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

    public function deleteCalendarAttendee(string $updated_by_user_id, array $payload)
    {
        $this->recordThat(new CalendarAttendeeDeleted($this->uuid(), $updated_by_user_id, $payload));

        return $this;
    }

    public function acceptCalendarEvent(string $updated_by_user_id, array $payload)
    {
        $this->recordThat(new CalendarAttendeeAccepted($this->uuid(), $updated_by_user_id, $payload));

        return $this;
    }

    public function declineCalendarEvent(string $updated_by_user_id, array $payload)
    {
        $this->recordThat(new CalendarAttendeeDeclined($this->uuid(), $updated_by_user_id, $payload));

        return $this;
    }
}
