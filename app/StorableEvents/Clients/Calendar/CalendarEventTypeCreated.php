<?php

namespace App\StorableEvents\Clients\Calendar;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class CalendarEventTypeCreated extends ShouldBeStored
{
    public $client, $name, $desc, $type;

    public function __construct($client, $name, $desc, $type)
    {
        $this->client = $client;
        $this->name = $name;
        $this->desc = $desc;
        $this->type = $type;
    }
}
