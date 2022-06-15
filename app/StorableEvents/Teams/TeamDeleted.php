<?php

namespace App\StorableEvents\Teams;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class TeamDeleted extends ShouldBeStored
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
