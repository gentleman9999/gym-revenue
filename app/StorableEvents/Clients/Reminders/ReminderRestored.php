<?php

namespace App\StorableEvents\Clients\Reminders;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class ReminderRestored extends ShouldBeStored
{
    public $client;
    public $user;
    public $id;

    public function __construct(string $client, string $user, string $id)
    {
        $this->client = $client;
        $this->user = $user;
        $this->id = $id;
    }
}
