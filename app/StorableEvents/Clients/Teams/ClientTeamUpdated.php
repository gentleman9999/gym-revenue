<?php

namespace App\StorableEvents\Clients\Teams;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class ClientTeamUpdated extends ShouldBeStored
{
    //user = request's current_user
    public $client;
    public $user;
    public $payload;

    public function __construct(string $client, string $user, array $payload)
    {
        $this->client = $client;
        $this->user = $user;
        $this->payload = $payload;
    }
}
