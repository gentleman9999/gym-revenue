<?php

namespace App\StorableEvents\Endusers;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LeadClaimedByRep extends ShouldBeStored
{
    public $lead, $user, $client;

    public function __construct(string $lead, string $user, string $client)
    {
        $this->lead = $lead;
        $this->user = $user;
        $this->client = $client;
    }
}
