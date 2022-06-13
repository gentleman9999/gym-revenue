<?php

namespace App\Domain\Reminders\Events;

use App\Domain\Reminders\Reminder;
use App\StorableEvents\EntityUpdated;

class ReminderUpdated extends EntityUpdated
{
    protected function getEntity(): string
    {
        return Reminder::class;
    }
}
