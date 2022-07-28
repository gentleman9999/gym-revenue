<?php

namespace App\Domain\Roles\Events;

use App\Domain\Roles\Role;
use App\StorableEvents\EntityUpdated;

class RoleUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return Role::class;
    }
}
