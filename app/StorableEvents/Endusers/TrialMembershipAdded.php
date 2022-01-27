<?php

namespace App\StorableEvents\Endusers;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class TrialMembershipAdded extends ShouldBeStored
{
    public $lead, $trial;

    public function __construct($lead, $trial)
    {
        $this->lead = $lead;
        $this->trial = $trial;
    }
}
