<?php

declare(strict_types=1);

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
