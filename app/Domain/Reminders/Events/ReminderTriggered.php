<?php

declare(strict_types=1);

namespace App\Domain\Reminders\Events;

use App\Domain\Reminders\Reminder;
use App\StorableEvents\GymRevCrudEvent;

class ReminderTriggered extends GymRevCrudEvent
{
    public $id;

    public function __construct(string $id)
    {
        parent::__construct();
        $this->id = $id;
    }

    public function getEntity(): string
    {
        return Reminder::class;
    }

    protected function getOperation(): string
    {
        return "TRIGGERED";
    }
}
