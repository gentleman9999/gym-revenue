<?php

declare(strict_types=1);

namespace App\Domain\CalendarAttendees;

use App\Domain\CalendarAttendees\Events\CalendarAttendeeAccepted;
use App\Domain\CalendarAttendees\Events\CalendarAttendeeAdded;
use App\Domain\CalendarAttendees\Events\CalendarAttendeeDeclined;
use App\Domain\CalendarAttendees\Events\CalendarAttendeeDeleted;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use Spatie\EventSourcing\Facades\Projectionist;

class CalendarAttendeeProjector extends Projector
{
    public function onCalendarAttendeeAdded(CalendarAttendeeAdded $event): void
    {
        $calendarAttendee            = (new CalendarAttendee())->writeable();
        $calendarAttendee->id        = $event->aggregateRootUuid();
        $calendarAttendee->client_id = $event->clientId();
        $calendarAttendee->fill($event->payload);
        if (Projectionist::isReplaying()) {
            unset($event->payload['entity_data']['relations']);
            unset($event->payload['entity_data']['connection']);
        }
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
