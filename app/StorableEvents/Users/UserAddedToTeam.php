<?php

namespace App\StorableEvents\Users;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserAddedToTeam extends ShouldBeStored
{
    public $user, $team, $name, $client;

    public function __construct($user, $team, $name, $client = null)
    {
        $this->user = $user;
        $this->team = $team;
        $this->name = $name;
        $this->client = $client;
    }
}
