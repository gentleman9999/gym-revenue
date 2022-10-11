<?php

namespace App\Domain\Chat\Events;

use App\Domain\Chat\Models\ChatMessage;
use App\StorableEvents\EntityDeleted;

class MessageDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return ChatMessage::class;
    }
}
