<?php

namespace App\StorableEvents\Endusers\Leads;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LeadTrashed extends ShouldBeStored
{
    public $user;
    public $id;
    public $reason;

    public function __construct(string $id, string $user, string $reason)
    {
        $this->user = $user;
        $this->id = $id;
        $this->reason = $reason;
    }
}
