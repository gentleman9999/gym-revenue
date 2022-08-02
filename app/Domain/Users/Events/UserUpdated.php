<?php

namespace App\Domain\Users\Events;

use App\Domain\Users\Models\User;
use App\StorableEvents\EntityUpdated;

class UserUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return User::class;
    }
}
