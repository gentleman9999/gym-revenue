<?php

declare(strict_types=1);

namespace App\Domain\GymAmenities\Events;

use App\Domain\GymAmenities\Projections\GymAmenity;
use App\StorableEvents\EntityDeleted;

class GymAmenityDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return GymAmenity::class;
    }
}
