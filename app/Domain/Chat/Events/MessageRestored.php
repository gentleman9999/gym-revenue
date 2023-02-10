<?php

declare(strict_types=1);

namespace App\Domain\Chat\Events;

use App\Domain\Chat\Models\ChatMessage;
use App\StorableEvents\EntityRestored;

class MessageRestored extends EntityRestored
{
    public function getEntity(): string
    {
        return ChatMessage::class;
    }
}
