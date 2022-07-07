<?php

namespace App\Domain\Users\Events;

use App\Domain\Users\Models\User;
use App\StorableEvents\EntityCreated;

class UserCreated extends EntityCreated
{
    protected function getEntity(): string
    {
        return User::class;
    }
}
