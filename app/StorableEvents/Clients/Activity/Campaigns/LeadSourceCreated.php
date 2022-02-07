<?php

namespace App\StorableEvents\Clients\Activity\Campaigns;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LeadSourceCreated extends ShouldBeStored
{
    public $client, $data, $user;

    public function __construct($client, $data, $user)
    {
        $this->client = $client;
        $this->data = $data;
        $this->user = $user;
    }
}
