<?php

namespace App\Domain\Chat\Events;

use App\Domain\Chat\Models\ChatMessage;
use App\StorableEvents\EntityCreated;

class MessageCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return ChatMessage::class;
    }
}
