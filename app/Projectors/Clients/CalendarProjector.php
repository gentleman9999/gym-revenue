<?php

namespace App\Projectors\Clients;

use App\Models\Calendar\CalendarEvent;
use App\StorableEvents\Clients\Calendar\CalendarEventCreated;
use App\StorableEvents\Clients\Calendar\CalendarEventDeleted;
use App\StorableEvents\Clients\Calendar\CalendarEventRestored;
use App\StorableEvents\Clients\Calendar\CalendarEventTrashed;
use App\StorableEvents\Clients\Calendar\CalendarEventUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class CalendarProjector extends Projector
{
    public function onCalenderEventCreated(CalendarEventCreated $event)
    {
        CalendarEvent::create($event->data);
    }

    public function onCalenderEventUpdated(CalendarEventUpdated $event)
    {
        CalendarEvent::findOrFail($event->data['id'])->updateOrFail($event->data);
    }

    public function onCalenderEventTrashed(CalendarEventTrashed $event)
    {
        CalendarEvent::findOrFail($event->id)->deleteOrFail();
    }

    public function onCalenderEventRestored(CalendarEventRestored $event)
    {
        CalendarEvent::withTrashed()->findOrFail($event->id)->restore();
    }

    public function onCalenderEventDeleted(CalendarEventDeleted $event)
    {
        CalendarEvent::withTrashed()->findOrFail($event->id)->forceDelete();
    }
}
