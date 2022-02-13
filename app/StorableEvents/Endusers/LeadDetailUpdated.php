<?php

namespace App\StorableEvents\Endusers;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LeadDetailUpdated extends ShouldBeStored
{
    public $lead, $key, $value, $user, $client;

    public function __construct(string $lead, string $key, string $value, string $user, string $client)
    {
        $this->lead = $lead;
        $this->key = $key;
        $this->value = $value;
        $this->user = $user;
        $this->client = $client;
    }
}
