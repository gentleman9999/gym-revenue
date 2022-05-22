<?php

namespace App\StorableEvents\Endusers\Leads;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class TrialMembershipAdded extends ShouldBeStored
{
    public $client;
    public $lead;
    public $trial;
    public $date;

    public function __construct($lead, $client, $trial, $date)
    {
        $this->lead = $lead;
        $this->client = $client;
        $this->trial = $trial;
        $this->date = $date;
    }
}
