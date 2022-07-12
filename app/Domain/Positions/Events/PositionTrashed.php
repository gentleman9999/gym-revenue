<?php

namespace App\Domain\Positions\Events;

use App\Models\Position;
use App\StorableEvents\EntityTrashed;

class PositionTrashed extends EntityTrashed
{
    protected function getEntity(): string
    {
        return Position::class;
    }
}
