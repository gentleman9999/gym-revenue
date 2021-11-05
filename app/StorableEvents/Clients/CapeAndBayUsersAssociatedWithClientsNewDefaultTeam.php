<?php

namespace App\StorableEvents\Clients;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class CapeAndBayUsersAssociatedWithClientsNewDefaultTeam extends ShouldBeStored
{
    public $client, $team, $payload;

    public function __construct(string $client, string $team, array $payload)
    {
        $this->client = $client;
        $this->team = $team;
        $this->payload = $payload;
    }
}
