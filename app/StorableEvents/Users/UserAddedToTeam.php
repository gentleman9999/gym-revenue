<?php

namespace App\StorableEvents\Users;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserAddedToTeam extends ShouldBeStored
{
    public $user;
    public $team;
    public $client;

    public function __construct($team, $user, $client = null)
    {
        $this->user = $user;
        $this->team = $team;
        $this->client = $client;
    }
}
