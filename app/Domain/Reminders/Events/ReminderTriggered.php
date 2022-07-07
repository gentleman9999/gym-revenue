<?php

namespace App\Domain\Reminders\Events;

use App\Domain\Reminders\Reminder;
use App\StorableEvents\GymRevCrudEvent;

class ReminderTriggered extends GymRevCrudEvent
{
    public $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    protected function getEntity(): string
    {
        return Reminder::class;
    }

    protected function getOperation(): string
    {
        return "TRIGGERED";
    }
}
