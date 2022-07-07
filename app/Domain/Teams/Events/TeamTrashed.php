<?php

namespace App\Domain\Teams\Events;

use App\Domain\Teams\Models\Team;
use App\StorableEvents\EntityTrashed;

class TeamTrashed extends EntityTrashed
{
    protected function getEntity(): string
    {
        return Team::class;
    }
}
