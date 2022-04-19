<?php

namespace App\StorableEvents\Users\Reminder;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class ReminderDeleted extends ShouldBeStored
{
    public $user, $id;

    public function __construct(string $user, string $id)
    {
        $this->user = $user;
        $this->id = $id;
    }
}
