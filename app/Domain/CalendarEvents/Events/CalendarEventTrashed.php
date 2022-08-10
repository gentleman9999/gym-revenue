<?php

namespace App\Domain\CalendarEvents\Events;

use App\Domain\CalendarEvents\CalendarEvent;
use App\StorableEvents\EntityTrashed;

class CalendarEventTrashed extends EntityTrashed
{
    public function getEntity(): string
    {
        return CalendarEvent::class;
    }
}
