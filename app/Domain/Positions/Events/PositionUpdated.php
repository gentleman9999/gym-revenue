<?php

namespace App\Domain\Positions\Events;

use App\Models\Position;
use App\StorableEvents\EntityUpdated;

class PositionUpdated extends EntityUpdated
{
    protected function getEntity(): string
    {
        return Position::class;
    }
}
