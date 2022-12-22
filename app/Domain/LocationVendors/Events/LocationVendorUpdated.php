<?php

declare(strict_types=1);

namespace App\Domain\LocationVendors\Events;

use App\Models\LocationVendor;
use App\StorableEvents\EntityUpdated;

class LocationVendorUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return LocationVendor::class;
    }
}
