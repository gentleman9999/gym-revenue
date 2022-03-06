<?php

namespace App\StorableEvents\Clients\Calendar;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class CalendarEventCreated extends ShouldBeStored
{
    public $user, $data, $client;

    public function __construct(string $client, string $user, array $data)
    {
        $this->client = $client;
        $this->user = $user;
        $this->data = $data;
    }
}
