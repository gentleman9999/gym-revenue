<?php

namespace App\Domain\Departments\Events;

use App\Models\Position;
use App\StorableEvents\EntityCreated;

class DepartmentCreated extends EntityCreated
{
    protected function getEntity(): string
    {
        return Position::class;
    }
}
