<?php

namespace App\Domain\Teams\Events;

use App\Domain\Teams\Models\Team;
use App\StorableEvents\EntityCreated;

class TeamCreated extends EntityCreated
{
    protected function getEntity(): string
    {
        return Team::class;
    }
}
