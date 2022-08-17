<?php

namespace App\Domain\CalendarAttendees\Events;

use App\Domain\CalendarAttendees\CalendarAttendee;
use App\StorableEvents\GymRevCrudEvent;

class CalendarAttendeeInvited extends GymRevCrudEvent
{
    public function getEntity(): string
    {
        return CalendarAttendee::class;
    }

    protected function getOperation(): string
    {
        return "INVITED";
    }
}
