<?php

namespace App\Domain\CalendarEventTypes\Events;

use App\Domain\CalendarEventTypes\CalendarEventType;
use App\StorableEvents\EntityUpdated;

class CalendarEventTypeUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return CalendarEventType::class;
    }
}
