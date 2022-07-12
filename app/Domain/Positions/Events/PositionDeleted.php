<?php

namespace App\Domain\Positions\Events;

use App\Models\Position;
use App\StorableEvents\EntityDeleted;

class PositionDeleted extends EntityDeleted
{
    protected function getEntity(): string
    {
        return Position::class;
    }
}
