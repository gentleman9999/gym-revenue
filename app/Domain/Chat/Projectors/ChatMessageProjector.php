<?php

declare(strict_types=1);

namespace App\Domain\Chat\Projectors;

use App\Domain\Chat\Events\MessageCreated;
use App\Domain\Chat\Events\MessageDeleted;
use App\Domain\Chat\Events\MessageRestored;
use App\Domain\Chat\Events\MessageUpdated;
use App\Domain\Chat\Models\Chat;
use App\Domain\Chat\Models\ChatMessage;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ChatMessageProjector extends Projector
{
    public function onMessageCreated(MessageCreated $event): void
    {
        (new ChatMessage())->fill($event->payload + [
            'id' => $event->aggregateRootUuid(),
            // Message should be marked as read for sender.
            'read_by' => [$event->payload['chat_participant_id']],
        ])->writeable()->save();

        // Update chat updated_at
        Chat::find($event->payload['chat_id'])->writeable()->touch();
    }

    public function onChatUpdated(MessageUpdated $event): void
    {
        ChatMessage::findOrFail($event->aggregateRootUuid())->writeable()->updateOrFail($event->payload);
    }

    public function onChatDeleted(MessageDeleted $event): void
    {
        ChatMessage::findOrFail($event->aggregateRootUuid())->writeable()->deleteOrFail();
    }

    public function onMessageRestored(MessageRestored $event): void
    {
        ChatMessage::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }
}
