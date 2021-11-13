<?php

namespace App\StorableEvents\Endusers;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class ManualLeadMade extends ShouldBeStored
{
    public $id, $lead, $user;
    public function __construct(string $id, array $lead, string $user)
    {
        $this->id = $id;
        $this->lead = $lead;
        $this->user = $user;
    }
}
