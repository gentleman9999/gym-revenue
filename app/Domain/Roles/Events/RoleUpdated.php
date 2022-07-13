<?php

namespace App\Domain\Roles\Events;

use App\Domain\Roles\Role;
use App\StorableEvents\EntityUpdated;

class RoleUpdated extends EntityUpdated
{
    protected function getEntity(): string
    {
        return Role::class;
    }
}
