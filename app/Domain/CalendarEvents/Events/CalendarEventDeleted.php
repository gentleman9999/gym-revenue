<?php

namespace App\Domain\CalendarEvents\Events;

use App\Domain\CalendarEvents\CalendarEvent;
use App\StorableEvents\EntityDeleted;

class CalendarEventDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return CalendarEvent::class;
    }
}
