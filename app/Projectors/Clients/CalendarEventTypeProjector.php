<?php

namespace App\Projectors\Clients;

use App\Models\Calendar\CalendarEventType;
use App\StorableEvents\Clients\Calendar\CalendarEventTypes\CalendarEventTypeCreated;
use App\StorableEvents\Clients\Calendar\CalendarEventTypes\CalendarEventTypeDeleted;
use App\StorableEvents\Clients\Calendar\CalendarEventTypes\CalendarEventTypeRestored;
use App\StorableEvents\Clients\Calendar\CalendarEventTypes\CalendarEventTypeTrashed;
use App\StorableEvents\Clients\Calendar\CalendarEventTypes\CalendarEventTypeUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class CalendarEventTypeProjector extends Projector
{
    public function onCalendarEventTypeCreated(CalendarEventTypeCreated $event)
    {
        CalendarEventType::create($event->data);
    }

    public function onCalendarEventTypeUpdated(CalendarEventTypeUpdated $event)
    {
        CalendarEventType::findOrFail($event->data['id'])->updateOrFail($event->data);
    }

    public function onCalendarEventTypeTrashed(CalendarEventTypeTrashed $event)
    {
        CalendarEventType::findOrFail($event->id)->deleteOrFail();
    }

    public function onCalendarEventTypeRestored(CalendarEventTypeRestored $event)
    {
        CalendarEventType::withTrashed()->findOrFail($event->id)->restore();
    }

    public function onCalendarEventTypeDeleted(CalendarEventTypeDeleted $event)
    {
        CalendarEventType::withTrashed()->findOrFail($event->id)->forceDelete();
    }
}
