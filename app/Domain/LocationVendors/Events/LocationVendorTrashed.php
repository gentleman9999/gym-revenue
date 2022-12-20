<?php

declare(strict_types=1);

namespace App\Domain\LocationVendors\Events;

use App\Models\LocationVendor;
use App\StorableEvents\EntityTrashed;

class LocationVendorTrashed extends EntityTrashed
{
    public function getEntity(): string
    {
        return LocationVendor::class;
    }
}
