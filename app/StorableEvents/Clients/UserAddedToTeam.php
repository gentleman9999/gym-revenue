<?php

namespace App\StorableEvents\Clients;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserAddedToTeam extends ShouldBeStored
{
    public $client;
    public $user;
    public $team;
    public $payload;

    public function __construct(string $client, string $user, string $team, array $payload)
    {
        $this->client = $client;
        $this->user = $user;
        $this->team = $team;
        $this->payload = $payload;
    }
}
