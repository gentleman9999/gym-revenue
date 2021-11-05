<?php

namespace App\StorableEvents\Clients;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class DefaultClientTeamCreated extends ShouldBeStored
{
    public $client, $team;

    public function __construct(string $client, string $team)
    {
        $this->client = $client;
        $this->team = $team;
    }
}
