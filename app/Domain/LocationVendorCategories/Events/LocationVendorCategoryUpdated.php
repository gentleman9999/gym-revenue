<?php

declare(strict_types=1);

namespace App\Domain\LocationVendorCategories\Events;

use App\Domain\LocationVendorCategories\Projections\LocationVendorCategory;
use App\StorableEvents\EntityUpdated;

class LocationVendorCategoryUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return LocationVendorCategory::class;
    }
}
