<?php

namespace App\Projectors\Clients;

use App\Models\CalendarEventType;
use App\StorableEvents\Clients\Calendar\CalendarEventTypes\CalendarEventTypeCreated;
use App\StorableEvents\Clients\Calendar\CalendarEventTypes\CalendarEventTypeDeleted;
use App\StorableEvents\Clients\Calendar\CalendarEventTypes\CalendarEventTypeRestored;
use App\StorableEvents\Clients\Calendar\CalendarEventTypes\CalendarEventTypeTrashed;
use App\StorableEvents\Clients\Calendar\CalendarEventTypes\CalendarEventTypeUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class CalendarEventTypeProjector extends Projector
{

    public function onCalenderEventTypeCreated(CalendarEventTypeCreated $event)
    {
        CalendarEventType::create($event->data);
    }

    public function onCalenderEventTypeUpdated(CalendarEventTypeUpdated $event)
    {
        CalendarEventType::findOrFail($event->data['id'])->updateOrFail($event->data);
    }

    public function onCalenderEventTypeTrashed(CalendarEventTypeTrashed $event)
    {
        CalendarEventType::findOrFail($event->id)->deleteOrFail();
    }

    public function onCalenderEventTypeRestored(CalendarEventTypeRestored $event)
    {
        CalendarEventType::withTrashed()->findOrFail($event->id)->restore();
    }

    public function onCalenderEventTypeDeleted(CalendarEventTypeDeleted $event)
    {
        CalendarEventType::withTrashed()->findOrFail($event->id)->forceDelete();
    }
}

