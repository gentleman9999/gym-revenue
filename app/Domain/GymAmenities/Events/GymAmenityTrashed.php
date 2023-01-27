<?php

declare(strict_types=1);

namespace App\Domain\GymAmenities\Events;

use App\Domain\GymAmenities\Projections\GymAmenity;
use App\StorableEvents\EntityTrashed;

class GymAmenityTrashed extends EntityTrashed
{
    public function getEntity(): string
    {
        return GymAmenity::class;
    }
}
