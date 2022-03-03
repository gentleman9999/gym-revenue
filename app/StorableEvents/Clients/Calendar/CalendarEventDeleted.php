<?php

namespace App\StorableEvents\Clients\Calendar;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class CalendarEventDeleted extends ShouldBeStored
{
    public $id, $client;

    public function __construct($client, $id)
    {
        $this->client = $client;
        $this->id = $id;
    }
}
