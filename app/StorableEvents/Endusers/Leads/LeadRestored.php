<?php

namespace App\StorableEvents\Endusers\Leads;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LeadRestored extends ShouldBeStored
{
    public $user;
    public $id;

    public function __construct(string $user, $id)
    {
        $this->user = $user;
        $this->id = $id;
    }
}
