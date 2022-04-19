<?php

namespace App\StorableEvents\Users\Reminder;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class ReminderTriggered extends ShouldBeStored
{
    public $id, $user;

    public function __construct(string $user, string $id)
    {
        $this->id = $id;
        $this->user = $user;
    }
}
