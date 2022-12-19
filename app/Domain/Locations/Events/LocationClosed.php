<?php

namespace App\Domain\Locations\Events;

use App\Models\Location;
use App\StorableEvents\EntityTrashed;

class LocationClosed extends EntityTrashed
{
    public function getEntity(): string
    {
        return Location::class;
    }
}