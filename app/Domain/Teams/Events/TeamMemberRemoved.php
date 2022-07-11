<?php

namespace App\Domain\Teams\Events;

use App\Domain\Teams\Models\Team;
use App\StorableEvents\GymRevCrudEvent;

class TeamMemberRemoved extends GymRevCrudEvent
{
    public $id;

    public function __construct(string $id)
    {
        parent::__construct();
        $this->id = $id;
    }

    protected function getEntity(): string
    {
        return Team::class;//TODO:should this be User? or maybe change operation to MEMBER_REMOVED?
    }

    protected function getOperation(): string
    {
        return "USER_REMOVED";
    }
}
