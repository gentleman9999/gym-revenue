<?php

namespace App\StorableEvents\Endusers;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LeadUpdated extends ShouldBeStored
{
    public $user, $data, $oldData;
    public function __construct(string $user, array $data, array $oldData)
    {
        $this->user = $user;
        $this->data = $data;
        $this->oldData = $oldData;
    }
}
