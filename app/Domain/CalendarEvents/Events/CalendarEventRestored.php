<?php

namespace App\Domain\CalendarEvents\Events;

use App\Domain\CalendarEvents\CalendarEvent;
use App\StorableEvents\EntityRestored;

class CalendarEventRestored extends EntityRestored
{
    public function getEntity(): string
    {
        return CalendarEvent::class;
    }
}
