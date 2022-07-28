<?php

namespace App\Domain\Reminders\Events;

use App\Domain\Reminders\Reminder;
use App\StorableEvents\EntityCreated;

class ReminderCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return Reminder::class;
    }
}
