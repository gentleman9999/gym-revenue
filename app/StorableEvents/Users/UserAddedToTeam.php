<?php

namespace App\StorableEvents\Users;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserAddedToTeam extends ShouldBeStored
{
    public $user;
    public $team;
    public $name;
    public $client;

    public function __construct(string $user, string $team, string $name, $client = null)
    {
        $this->user = $user;
        $this->team = $team;
        $this->name = $name;
        $this->client = $client;
    }
}
