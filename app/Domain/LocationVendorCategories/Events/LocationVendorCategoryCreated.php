<?php

declare(strict_types=1);

namespace App\Domain\LocationVendorCategories\Events;

use App\Domain\LocationVendorCategories\Projections\LocationVendorCategory;
use App\StorableEvents\EntityCreated;

class LocationVendorCategoryCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return LocationVendorCategory::class;
    }
}
