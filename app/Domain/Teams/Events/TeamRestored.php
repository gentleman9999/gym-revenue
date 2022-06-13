<?php

namespace App\Domain\Teams\Events;

use App\Domain\Teams\Models\Team;
use App\StorableEvents\EntityRestored;

class TeamRestored extends EntityRestored
{
    protected function getEntity(): string
    {
        return Team::class;
    }
}
