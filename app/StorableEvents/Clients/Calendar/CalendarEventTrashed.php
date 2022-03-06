<?php

namespace App\StorableEvents\Clients\Calendar;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class CalendarEventTrashed extends ShouldBeStored
{
    public $user, $id, $client;
    public function __construct(string $client, string $user, $id)
    {
        $this->client = $client;
        $this->user = $user;
        $this->id = $id;
    }
}
