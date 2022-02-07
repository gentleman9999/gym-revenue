<?php

namespace App\StorableEvents\Endusers;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class TrialMembershipUsed extends ShouldBeStored
{
    public $client, $lead, $trial, $date;

    public function __construct($lead, $client, $trial, $date)
    {
        $this->lead = $lead;
        $this->client = $client;
        $this->trial = $trial;
        $this->date = $date;
    }
}
