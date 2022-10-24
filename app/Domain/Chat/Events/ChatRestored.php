<?php

namespace App\Domain\Chat\Events;

use App\Domain\Chat\Models\Chat;
use App\StorableEvents\EntityRestored;

class ChatRestored extends EntityRestored
{
    public function getEntity(): string
    {
        return Chat::class;
    }
}
