<?php

namespace App\Domain\Roles\Events;

use App\Domain\Roles\Role;
use App\StorableEvents\EntityCreated;

class RoleCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return Role::class;
    }
}
