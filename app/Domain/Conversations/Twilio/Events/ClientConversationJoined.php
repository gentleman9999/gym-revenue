<?php

declare(strict_types=1);

namespace App\Domain\Conversations\Twilio\Events;

use App\Domain\Conversations\Twilio\Models\ClientConversation;
use App\StorableEvents\EntityCreated;

class ClientConversationJoined extends EntityCreated
{
    public function getEntity(): string
    {
        return ClientConversation::class;
    }
}
