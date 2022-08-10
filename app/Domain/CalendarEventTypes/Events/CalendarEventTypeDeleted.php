<?php

namespace App\Domain\CalendarEventTypes\Events;

use App\Domain\CalendarEventTypes\CalendarEventType;
use App\StorableEvents\EntityDeleted;

class CalendarEventTypeDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return CalendarEventType::class;
    }
}
