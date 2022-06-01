<?php

namespace App\StorableEvents\Endusers\Leads;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LeadSubscribedToComms extends ShouldBeStored
{
    public $lead;
    public $date;

    public function __construct(string $lead, string $date)
    {
        $this->lead = $lead;
        $this->date = $date;
    }
}
