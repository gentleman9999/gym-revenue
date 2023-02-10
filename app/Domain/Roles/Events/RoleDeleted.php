<?php

declare(strict_types=1);

namespace App\Domain\Roles\Events;

use App\Domain\Roles\Role;
use App\StorableEvents\EntityDeleted;

class RoleDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return Role::class;
    }
}
