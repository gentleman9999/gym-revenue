<?php

declare(strict_types=1);

namespace App\Domain\Chat\Events;

use App\Domain\Chat\Models\ChatParticipant;
use App\StorableEvents\EntityCreated;

class ChatParticipantCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return ChatParticipant::class;
    }
}
