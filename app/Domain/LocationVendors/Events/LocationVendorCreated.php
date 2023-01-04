<?php

declare(strict_types=1);

namespace App\Domain\LocationVendors\Events;

use App\Models\LocationVendor;
use App\StorableEvents\EntityCreated;

class LocationVendorCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return LocationVendor::class;
    }
}
