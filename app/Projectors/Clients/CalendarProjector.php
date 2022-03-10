<?php

namespace App\Projectors\Clients;

use App\Models\CalendarEvent;
use App\Models\CalendarEventType;
use App\StorableEvents\Clients\Calendar\CalendarEventCreated;
use App\StorableEvents\Clients\Calendar\CalendarEventTypeCreated;
use App\StorableEvents\Clients\Calendar\CalendarEventDeleted;
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

    public function onCalenderEventTrashed(CalendarEventDeleted $event)
    {
        CalendarEvent::findOrFail($event->id)->deleteOrFail();
    }

    public function onCalenderEventRestored(CalendarEventDeleted $event)
    {
        CalendarEvent::withTrashed()->findOrFail($event->id)->restore();
    }

    public function onCalenderEventDeleted(CalendarEventDeleted $event)
    {
        CalendarEvent::withTrashed()->findOrFail($event->id)->forceDelete();
    }

    public function onCalenderEventTypeCreated(CalendarEventTypeCreated $event)
    {
        CalendarEventType::create($event->data);
    }
}

