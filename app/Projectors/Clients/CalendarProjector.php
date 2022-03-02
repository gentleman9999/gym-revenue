<?php

namespace App\Projectors\Clients;

use App\Models\CalendarEvent;
use App\StorableEvents\Clients\Calendar\CalendarEventCreated;

use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class CalendarProjector extends Projector
{
    public function onCalenderEventCreated(CalendarEventCreated $event)
    {
        $event = CalendarEvent::create([
            'client_id' => $event->client,
            'title' => $event->title,
            'full_day_event' => false,
            'start' => $event->start,
            'end' => $event->end,
        ]);
    }
}

