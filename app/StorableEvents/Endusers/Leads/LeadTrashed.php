<?php

namespace App\StorableEvents\Endusers\Leads;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LeadTrashed extends ShouldBeStored
{
    public $user, $id;
    public function __construct(string $user, $id)
    {
        $this->user = $user;
        $this->id = $id;
    }
}
