<?php

namespace App\StorableEvents\Clients\Reminder;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class ReminderDeleted extends ShouldBeStored
{
    public $client, $user, $id;

    public function __construct(string $client, string $user, string $id)
    {
        $this->client = $client;
        $this->user = $user;
        $this->id = $id;
    }
}
