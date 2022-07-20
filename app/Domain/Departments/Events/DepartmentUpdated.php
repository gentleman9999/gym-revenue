<?php

namespace App\Domain\Departments\Events;

use App\Models\Position;
use App\StorableEvents\EntityUpdated;

class DepartmentUpdated extends EntityUpdated
{
    protected function getEntity(): string
    {
        return Position::class;
    }
}