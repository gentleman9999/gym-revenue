<?php

namespace App\Domain\Teams\Events;

use App\Domain\Teams\Models\Team;
use App\StorableEvents\EntityDeleted;

class TeamDeleted extends EntityDeleted
{
    protected function getEntity(): string
    {
        return Team::class;
    }
}
