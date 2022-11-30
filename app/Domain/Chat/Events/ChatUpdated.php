<?php

namespace App\Domain\Chat\Events;

use App\Domain\Chat\Models\Chat;
use App\StorableEvents\EntityUpdated;

class ChatUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return Chat::class;
    }
}
