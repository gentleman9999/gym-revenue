<?php

namespace App\Domain\Locations\Events;

use App\Models\Location;
use App\StorableEvents\EntityCreated;

class LocationCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return Location::class;
    }
}
