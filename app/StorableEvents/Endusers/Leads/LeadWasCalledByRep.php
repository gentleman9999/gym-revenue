<?php

namespace App\StorableEvents\Endusers\Leads;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LeadWasCalledByRep extends ShouldBeStored
{
    public $lead;
    public $data;
    public $user;

    public function __construct(string $lead, array $data, string $user)
    {
        $this->lead = $lead;
        $this->data = $data;
        $this->user = $user;
    }
}
