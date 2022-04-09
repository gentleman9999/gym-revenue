<?php

namespace App\Projectors\Clients;

use App\Models\Calendar\CalendarAttendee;
use App\StorableEvents\Clients\Calendar\CalendarAttendeeAdded;
use App\StorableEvents\Clients\Calendar\CalendarAttendeeDeleted;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class CalendarAttendeeProjector extends Projector
{

    public function onCalendarAttendeeAdded(CalendarAttendeeAdded $event)
    {
        CalendarAttendee::create($event->data);
    }

    public function onCalendarAttendeeDeleted(CalendarAttendeeDeleted $event)
    {
        CalendarAttendee::whereEntityType($event->data['entity_type'])->whereEntityId($event->data['entity_id'])->forceDelete();
    }

}

