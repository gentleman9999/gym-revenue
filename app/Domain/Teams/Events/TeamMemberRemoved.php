<?php

namespace App\Domain\Teams\Events;

use App\Domain\Teams\Models\Team;
use App\StorableEvents\GymRevCrudEvent;

class TeamMemberRemoved extends GymRevCrudEvent
{
    public $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    protected function getEntity(): string
    {
        return Team::class;
    }

    protected function getOperation(): string
    {
        return "REMOVED";
    }
}
