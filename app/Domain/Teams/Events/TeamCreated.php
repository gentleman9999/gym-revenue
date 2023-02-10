<?php

declare(strict_types=1);

namespace App\Domain\Teams\Events;

use App\Domain\Teams\Models\Team;
use App\StorableEvents\EntityCreated;

class TeamCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return Team::class;
    }
}
