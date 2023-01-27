<?php

declare(strict_types=1);

namespace App\Domain\GymAmenities\Events;

use App\Domain\GymAmenities\Projections\GymAmenity;
use App\StorableEvents\EntityCreated;

class GymAmenityCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return GymAmenity::class;
    }
}
