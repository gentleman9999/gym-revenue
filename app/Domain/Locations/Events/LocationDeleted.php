<?php

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
