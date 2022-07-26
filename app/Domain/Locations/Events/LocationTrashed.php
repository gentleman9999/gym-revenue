<?php

namespace App\Domain\Locations\Events;

use App\Models\Location;
use App\StorableEvents\EntityTrashed;

class LocationTrashed extends EntityTrashed
{
    public function getEntity(): string
    {
        return Location::class;
    }
}
