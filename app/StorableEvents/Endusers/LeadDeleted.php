<?php

namespace App\StorableEvents\Endusers;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LeadDeleted extends ShouldBeStored
{
    public $user;
    public $id;
    public $data;

    public function __construct(string $user, string $id, $data)
    {
        $this->user = $user;
        $this->id = $id;
        $this->data = $data;
    }
}
