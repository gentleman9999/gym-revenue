<?php

declare(strict_types=1);

namespace App\Domain\Teams\Events;

use App\Domain\Teams\Models\Team;
use App\StorableEvents\EntityRestored;

class TeamRestored extends EntityRestored
{
    public function getEntity(): string
    {
        return Team::class;
    }
}
