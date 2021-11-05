<?php

namespace App\StorableEvents\Clients;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class TeamCreated extends DefaultClientTeamCreated
{
    public $name;

    public function __construct($client, $team, $name)
    {
        parent::__construct($client, $team);
        $this->name = $name;
    }
}
