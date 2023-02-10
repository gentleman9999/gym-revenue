<?php

declare(strict_types=1);

namespace App\Domain\Positions\Events;

use App\Models\Position;
use App\StorableEvents\EntityCreated;

class PositionCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return Position::class;
    }
}
