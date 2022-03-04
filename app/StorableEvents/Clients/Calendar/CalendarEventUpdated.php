<?php

namespace App\StorableEvents\Clients\Calendar;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class CalendarEventUpdated extends ShouldBeStored
{
    public $client, $id, $title, $start, $end, $options, $type;

    public function __construct($client, $id, $title, $start, $end, $options, $type)
    {
        $this->client = $client;
        $this->id = $id;
        $this->title = $title;
        $this->start = $start;
        $this->end = $end;
        $this->options = $options;
        $this->type = $type;
    }
}
