<?php

declare(strict_types=1);

namespace App\Domain\Positions\Events;

use App\Models\Position;
use App\StorableEvents\EntityUpdated;

class PositionUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return Position::class;
    }
}
