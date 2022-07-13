<?php

namespace App\Domain\Departments\Events;

use App\Models\Position;
use App\StorableEvents\EntityTrashed;

class DepartmentTrashed extends EntityTrashed
{
    protected function getEntity(): string
    {
        return Position::class;
    }
}
