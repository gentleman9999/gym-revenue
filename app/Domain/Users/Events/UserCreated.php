<?php

namespace App\Domain\Users\Events;

use App\Models\User;
use App\StorableEvents\EntityCreated;

class UserCreated extends EntityCreated
{
    protected function getEntity(): string
    {
        return User::class;
    }
}
