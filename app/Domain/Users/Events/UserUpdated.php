<?php

namespace App\Domain\Users\Events;

use App\Models\User;
use App\StorableEvents\EntityUpdated;

class UserUpdated extends EntityUpdated
{
    protected function getEntity(): string
    {
        return User::class;
    }
}
