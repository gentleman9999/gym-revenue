<?php

namespace App\Domain\Positions\Events;

use App\Models\Position;
use App\StorableEvents\EntityCreated;

class PositionCreated extends EntityCreated
{
    protected function getEntity(): string
    {
        return Position::class;
    }
}
