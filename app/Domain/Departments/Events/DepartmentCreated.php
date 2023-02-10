<?php

declare(strict_types=1);

namespace App\Domain\Departments\Events;

use App\Models\Position;
use App\StorableEvents\EntityCreated;

class DepartmentCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return Position::class;
    }
}
