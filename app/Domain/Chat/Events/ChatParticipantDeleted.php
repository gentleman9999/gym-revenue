<?php

declare(strict_types=1);

namespace App\Domain\Chat\Events;

use App\Domain\Chat\Models\ChatParticipant;
use App\StorableEvents\EntityDeleted;

class ChatParticipantDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return ChatParticipant::class;
    }
}
