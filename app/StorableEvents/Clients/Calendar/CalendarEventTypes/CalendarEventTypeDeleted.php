<?php

namespace App\StorableEvents\Clients\Calendar\CalendarEventTypes;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class CalendarEventTypeDeleted extends ShouldBeStored
{
    public $user, $id, $client;

    public function __construct(string $client, string $user, string $id)
    {
        $this->client = $client;
        $this->user = $user;
        $this->id = $id;
    }
}
