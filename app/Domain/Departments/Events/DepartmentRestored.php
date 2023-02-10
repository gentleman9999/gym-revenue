<?php

declare(strict_types=1);

namespace App\Domain\Departments\Events;

use App\Models\Position;
use App\StorableEvents\EntityRestored;

class DepartmentRestored extends EntityRestored
{
    public function getEntity(): string
    {
        return Position::class;
    }
}
