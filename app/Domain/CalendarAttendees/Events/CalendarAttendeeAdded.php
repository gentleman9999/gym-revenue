<?php

namespace App\Domain\CalendarAttendees\Events;

use App\Domain\CalendarAttendees\CalendarAttendee;
use App\StorableEvents\EntityCreated;

class CalendarAttendeeAdded extends EntityCreated
{
    public function getEntity(): string
    {
        return CalendarAttendee::class;
    }
}
