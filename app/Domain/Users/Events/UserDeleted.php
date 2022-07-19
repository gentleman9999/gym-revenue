<?php

namespace App\Domain\Users\Events;

use App\Domain\Users\Models\User;
use App\StorableEvents\EntityDeleted;

class UserDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return User::class;
    }
}
