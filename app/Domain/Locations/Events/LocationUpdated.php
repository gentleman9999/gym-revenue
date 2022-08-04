<?php

namespace App\Domain\Locations\Events;

use App\Models\Location;
use App\StorableEvents\EntityUpdated;

class LocationUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return Location::class;
    }
}
