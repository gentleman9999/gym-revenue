<?php

namespace App\Domain\CalendarAttendees\Events;

use App\Domain\CalendarAttendees\CalendarAttendee;
use App\StorableEvents\GymRevCrudEvent;

class CalendarAttendeeDeclined extends GymRevCrudEvent
{
    public function getEntity(): string
    {
        return CalendarAttendee::class;
    }

    protected function getOperation(): string
    {
        return "DECLINED";
    }
}
