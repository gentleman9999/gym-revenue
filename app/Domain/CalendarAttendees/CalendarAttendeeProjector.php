<?php

namespace App\Domain\CalendarAttendees;

use App\Domain\CalendarAttendees\Events\CalendarAttendeeAccepted;
use App\Domain\CalendarAttendees\Events\CalendarAttendeeAdded;
use App\Domain\CalendarAttendees\Events\CalendarAttendeeDeclined;
use App\Domain\CalendarAttendees\Events\CalendarAttendeeDeleted;
use App\Domain\CalendarEvents\CalendarEvent;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class CalendarAttendeeProjector extends Projector
{
    public function onCalendarAttendeeAdded(CalendarAttendeeAdded $event): void
    {
        $calendarAttendee = (new CalendarAttendee())->writeable();
        $temp = ['client_id' => CalendarEvent::findOrFail($event->payload['calendar_event_id'])->client_id, 'id' => $event->aggregateRootUuid()];
        $calendarAttendee->forceFill($temp);
        $calendarAttendee->fill($event->payload);
        $calendarAttendee->save();
    }

    public function onCalendarAttendeeDeleted(CalendarAttendeeDeleted $event): void
    {
        $calendarAttendee = CalendarAttendee::findOrFail($event->aggregateRootUuid())->writeable();
        $calendarAttendee->delete();
    }

    public function onCalendarInviteAccepted(CalendarAttendeeAccepted $event): void
    {
        CalendarAttendee::findOrFail($event->aggregateRootUuid())->writeable()->update(['invitation_status' => 'Accepted']);
    }

    public function onCalendarInviteDeclined(CalendarAttendeeDeclined $event): void
    {
        CalendarAttendee::findOrFail($event->aggregateRootUuid())->writeable()->update(['invitation_status' => 'Declined']);
    }
}
