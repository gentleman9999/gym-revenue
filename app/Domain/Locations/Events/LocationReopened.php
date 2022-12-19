<?php

namespace App\Domain\Locations\Events;

use App\Domain\Locations\Projections\Location;
use App\StorableEvents\EntityRestored;

class LocationReopened extends EntityRestored
{
    public function getEntity(): string
    {
        return Location::class;
    }
}
