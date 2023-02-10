<?php

declare(strict_types=1);

namespace App\Domain\CalendarEventTypes\Events;

use App\Domain\CalendarEventTypes\CalendarEventType;
use App\StorableEvents\EntityTrashed;

class CalendarEventTypeTrashed extends EntityTrashed
{
    public function getEntity(): string
    {
        return CalendarEventType::class;
    }
}
