<?php

namespace App\Domain\Roles\Events;

use App\Domain\Roles\Role;
use App\StorableEvents\EntityDeleted;

class RoleDeleted extends EntityDeleted
{
    protected function getEntity(): string
    {
        return Role::class;
    }
}