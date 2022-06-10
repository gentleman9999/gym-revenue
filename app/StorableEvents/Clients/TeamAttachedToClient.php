<?php

namespace App\StorableEvents\Clients;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class TeamAttachedToClient extends ShouldBeStored
{
    public $team;
    public $user;

    public function __construct(string $team, string|null $user = null)
    {
        $this->team = $team;
        $this->user = $user;
    }
}
