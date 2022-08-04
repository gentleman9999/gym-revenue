<?php

namespace App\Domain\Reminders\Events;

use App\Domain\Reminders\Reminder;
use App\StorableEvents\EntityUpdated;

class ReminderUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return Reminder::class;
    }
}
