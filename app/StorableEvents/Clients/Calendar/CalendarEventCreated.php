<?php

namespace App\StorableEvents\Clients\Calendar;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class CalendarEventCreated extends ShouldBeStored
{
    public $client, $title, $start, $end, $type;

    public function __construct($client, $title, $start, $end, $type)
    {
        $this->client = $client;
        $this->title = $title;
        $this->start = $start;
        $this->end = $end;
        $this->type = $type;
    }
}
