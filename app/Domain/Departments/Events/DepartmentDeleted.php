<?php

namespace App\Domain\Departments\Events;

use App\Models\Position;
use App\StorableEvents\EntityDeleted;

class DepartmentDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return Position::class;
    }
}
