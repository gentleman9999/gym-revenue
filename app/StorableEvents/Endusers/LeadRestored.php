<?php

namespace App\StorableEvents\Endusers;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LeadRestored extends ShouldBeStored
{
    public $user, $id;
    public function __construct(string $user, $id)
    {
        $this->user = $user;
        $this->id = $id;
    }
}
