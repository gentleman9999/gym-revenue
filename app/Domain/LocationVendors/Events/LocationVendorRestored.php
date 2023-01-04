<?php

declare(strict_types=1);

namespace App\Domain\LocationVendors\Events;

use App\Domain\LocationVendors\Projections\LocationVendor;
use App\StorableEvents\EntityRestored;

class LocationVendorRestored extends EntityRestored
{
    public function getEntity(): string
    {
        return LocationVendor::class;
    }
}
