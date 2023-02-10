<?php

declare(strict_types=1);

namespace App\Domain\Positions\Events;

use App\Models\Position;
use App\StorableEvents\EntityTrashed;

class PositionTrashed extends EntityTrashed
{
    public function getEntity(): string
    {
        return Position::class;
    }
}
