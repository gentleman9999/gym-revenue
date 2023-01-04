<?php

declare(strict_types=1);

namespace App\Domain\Chat\Projectors;

use App\Domain\Chat\Events\ChatParticipantCreated;
use App\Domain\Chat\Events\ChatParticipantDeleted;
use App\Domain\Chat\Models\ChatParticipant;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ChatParticipantProjector extends Projector
{
    public function onChatParticipantCreated(ChatParticipantCreated $event): void
    {
        $chat_participant = new ChatParticipant();
        $chat_participant->id = $event->aggregateRootUuid();
        $chat_participant->chat_id = $event->payload['chat_id'];
        $chat_participant->user_id = $event->payload['user_id'];
        $chat_participant->writeable()->save();
    }

    public function onChatParticipantDeleted(ChatParticipantDeleted $event): void
    {
        ChatParticipant::findOrFail($event->aggregateRootUuid())->writeable()->deleteOrFail();
    }
}
