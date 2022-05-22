<?php

namespace App\StorableEvents\Users\Reminder;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class ReminderUpdated extends ShouldBeStored
{
    public $user;
    public $payload;

    public function __construct(string $user, array $payload)
    {
        $this->user = $user;
        $this->payload = $payload;
    }
}
