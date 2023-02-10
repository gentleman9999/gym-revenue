<?php

declare(strict_types=1);

namespace App\Domain\Departments\Events;

use App\Models\Position;
use App\StorableEvents\EntityUpdated;

class DepartmentUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return Position::class;
    }
}
