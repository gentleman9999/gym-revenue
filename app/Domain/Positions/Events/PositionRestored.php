<?php

namespace App\Domain\Positions\Events;

use App\Models\Position;
use App\StorableEvents\EntityRestored;

class PositionRestored extends EntityRestored
{
    public function getEntity(): string
    {
        return Position::class;
    }
}
