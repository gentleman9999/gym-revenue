<?php

declare(strict_types=1);

namespace App\Domain\Chat\Projectors;

use App\Domain\Chat\Actions\CreateParticipant;
use App\Domain\Chat\Events\ChatCreated;
use App\Domain\Chat\Events\ChatDeleted;
use App\Domain\Chat\Events\ChatRestored;
use App\Domain\Chat\Events\ChatUpdated;
use App\Domain\Chat\Models\Chat;
use App\Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ChatProjector extends Projector
{
    public function onChatCreated(ChatCreated $event): void
    {
        DB::transaction(static function () use ($event) {
            $chat = new Chat();
            $chat->id = $event->aggregateRootUuid();
            $chat->client_id = $event->payload['client_id'];
            $chat->created_by = $event->payload['user_id'];
            $chat->admin_id = $event->payload['user_id'];
            $chat->writeable()->save();

            $chat = $chat->refresh();
            foreach (array_unique($event->payload['participant_ids']) as $user_id) {
                CreateParticipant::run($chat, User::find($user_id));
            }
        });
    }

    public function onChatUpdated(ChatUpdated $event): void
    {
        Chat::findOrFail($event->aggregateRootUuid())->writeable()->updateOrFail($event->payload);
    }

    public function onChatDeleted(ChatDeleted $event): void
    {
        Chat::findOrFail($event->aggregateRootUuid())->writeable()->deleteOrFail();
    }

    public function onChatRestored(ChatRestored $event): void
    {
        Chat::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }
}
