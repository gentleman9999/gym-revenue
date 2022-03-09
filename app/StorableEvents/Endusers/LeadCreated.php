<?php

namespace App\StorableEvents\Endusers;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LeadCreated extends ShouldBeStored
{
    public $user, $data;

    public function __construct(string $user, array $data)
    {
        $this->user = $user;
        $this->data = $data;
    }
}
