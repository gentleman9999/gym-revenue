<?php

namespace App\Domain\Users\Events;

use App\Domain\Users\Models\User;
use App\StorableEvents\EntityCreated;

class FileUploaded extends EntityCreated
{
    public function getEntity(): string
    {
        return User::class;
    }
}
