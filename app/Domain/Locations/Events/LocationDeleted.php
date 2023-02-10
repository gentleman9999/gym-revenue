<?php

declare(strict_types=1);

namespace App\Domain\Locations\Events;

use App\Models\Location;
use App\StorableEvents\EntityDeleted;

class LocationDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return Location::class;
    }
}
