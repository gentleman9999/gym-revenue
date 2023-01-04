<?php

declare(strict_types=1);

namespace App\Domain\LocationEmployees\Events;

use App\Domain\LocationEmployees\Projections\LocationEmployee;
use App\StorableEvents\EntityCreated;

class LocationEmployeeCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return LocationEmployee::class;
    }
}
