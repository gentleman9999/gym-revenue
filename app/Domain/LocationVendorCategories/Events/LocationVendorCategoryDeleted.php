<?php

declare(strict_types=1);

namespace App\Domain\LocationVendorCategories\Events;

use App\Domain\LocationVendorCategories\Projections\LocationVendorCategory;
use App\StorableEvents\EntityDeleted;

class LocationVendorCategoryDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return LocationVendorCategory::class;
    }
}
