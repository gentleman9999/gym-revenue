<?php

namespace App\Domain\Chat\Events;

use App\Domain\Chat\Models\ChatMessage;
use App\StorableEvents\EntityUpdated;

class MessageUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return ChatMessage::class;
    }
}
