<?php

namespace App\StorableEvents\Clients\Activity\Leads;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LeadStatusCreated extends ShouldBeStored
{
    public $client;
    public $data;
    public $user;

    public function __construct(string $client, array $data, string $user)
    {
        $this->client = $client;
        $this->data = $data;
        $this->user = $user;
    }
}
