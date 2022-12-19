<?php

declare(strict_types=1);

namespace App\Domain\Users\Events;

use App\Domain\Users\Models\User;
use App\StorableEvents\EntityTrashed;

class UserObfuscated extends EntityTrashed
{
    public function getEntity(): string
    {
        return User::class;
    }
}
