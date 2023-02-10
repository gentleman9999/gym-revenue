<?php

declare(strict_types=1);

namespace App\Domain\Teams\Events;

use App\Domain\Teams\Models\Team;
use App\StorableEvents\EntityTrashed;

class TeamTrashed extends EntityTrashed
{
    public function getEntity(): string
    {
        return Team::class;
    }
}
