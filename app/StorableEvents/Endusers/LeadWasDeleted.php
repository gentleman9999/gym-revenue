<?php

namespace App\StorableEvents\Endusers;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LeadWasDeleted extends ShouldBeStored
{
    public function __construct()
    {
    }
}
