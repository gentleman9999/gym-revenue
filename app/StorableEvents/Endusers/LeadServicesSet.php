<?php

namespace App\StorableEvents\Endusers;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LeadServicesSet extends ShouldBeStored
{
    public $user, $serviceIds;

    public function __construct($serviceIds, $user)
    {
        $this->user = $user;
        $this->serviceIds = $serviceIds;
    }
}
