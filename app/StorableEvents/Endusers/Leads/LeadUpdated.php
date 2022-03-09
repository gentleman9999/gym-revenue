<?php

namespace App\StorableEvents\Endusers\Leads;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LeadUpdated extends ShouldBeStored
{
    public $user, $data;
    public function __construct(string $user, array $data)
    {
        $this->user = $user;
        $this->data = $data;
    }
}
