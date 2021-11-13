<?php

namespace App\StorableEvents\Endusers;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LeadWasCalledByRep extends ShouldBeStored
{
    public $lead, $data, $user;

    public function __construct(string $lead, array $data, string $user)
    {
        $this->lead = $lead;
        $this->data = $data;
        $this->user = $user;
    }
}
