<?php

namespace App\Domain\Chat\Events;

use App\Domain\Chat\Models\Chat;
use App\StorableEvents\EntityDeleted;

class ChatDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return Chat::class;
    }
}
