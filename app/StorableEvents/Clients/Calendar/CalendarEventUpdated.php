<?php

namespace App\StorableEvents\Clients\Calendar;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class CalendarEventUpdated extends ShouldBeStored
{
    public $id, $client, $title, $start, $end;

    public function __construct($id, $client, $title, $start, $end)
    {
        $this->id = $id;
        $this->client = $client;
        $this->title = $title;
        $this->start = $start;
        $this->end = $end;
    }
}
