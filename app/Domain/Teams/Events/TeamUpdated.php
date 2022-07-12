<?php

namespace App\Domain\Teams\Events;

use App\Domain\Teams\Models\Team;
use App\StorableEvents\EntityUpdated;

class TeamUpdated extends EntityUpdated
{
    protected function getEntity(): string
    {
        return Team::class;
    }
}
