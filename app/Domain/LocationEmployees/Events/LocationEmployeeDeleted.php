<?php

declare(strict_types=1);

namespace App\Domain\LocationEmployees\Events;

use App\Domain\LocationEmployees\Projections\LocationEmployee;
use App\StorableEvents\EntityDeleted;

class LocationEmployeeDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return LocationEmployee::class;
    }
}
