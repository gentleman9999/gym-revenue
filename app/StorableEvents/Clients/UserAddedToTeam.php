<?php

namespace App\StorableEvents\Clients;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserAddedToTeam extends ShouldBeStored
{
    public $client, $user, $team, $payload;

    public function __construct(string $client, string $user, string $team, array $payload)
    {
        $this->client = $client;
        $this->user = $user;
        $this->team = $team;
        $this->payload = $payload;
    }
}
