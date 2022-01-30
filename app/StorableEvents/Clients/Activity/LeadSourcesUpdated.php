<?php

namespace App\StorableEvents\Clients\Activity\Campaigns;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LeadSourcesUpdated extends ShouldBeStored
{
    public $client, $sources, $user;

    public function __construct($client, $sources, $user)
    {
        $this->client = $client;
        $this->sources = $sources;
        $this->user = $user;
    }
}
