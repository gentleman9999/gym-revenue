<?php

namespace App\StorableEvents\Clients\Teams;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class ClientTeamDeleted extends ShouldBeStored
{
    //user = request's current_user
    public $client;
    public $user;
    public $id;

    public function __construct(string $client, string $user, string $id)
    {
        $this->client = $client;
        $this->user = $user;
        $this->id = $id;
    }
}
