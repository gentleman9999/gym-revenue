<?php

declare(strict_types=1);

namespace App\Domain\Chat\Actions;

use App\Domain\Chat\Aggregates\ChatParticipantAggregate;
use App\Domain\Chat\Models\Chat;
use App\Domain\Chat\Models\ChatParticipant;
use App\Domain\Notifications\Actions\CreateNotification;
use App\Domain\Notifications\Notification;
use App\Http\Middleware\InjectClientId;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteChatParticipant
{
    use AsAction;

    public function handle(ChatParticipant $chat_participant): bool
    {
        /** @var Chat $chat */
        $chat = $chat_participant->chat()->with(['participants.user'])->first();
        $participant_user_id = $chat_participant->user_id;
        $data = [
            'chat_id' => $chat->id,
            'participant' => $chat_participant,
            'type' => Notification::TYPE_DELETED_CHAT_PARTICIPANT,
        ];

        ChatParticipantAggregate::retrieve($chat_participant->id)->delete()->persist();

        $next_participant = $chat->participants->where('user_id', '!=', $participant_user_id);
        // If no participant left, we want to delete chat.
        if ($chat->participants->where('user_id', '!=', $participant_user_id)->count() < 2) {
            DeleteChat::run($chat);
            $data['type'] = Notification::TYPE_DELETED_CHAT;
        } elseif ($chat->admin_id === $participant_user_id) {
            // Assign a new admin if admin is removed.
            UpdateChat::run([
                'id' => $chat->id,
                'admin_id' => $next_participant->first()->id,
            ]);
        }


        $chat->participants->each(static function (ChatParticipant $participant) use ($data, $participant_user_id) {

            // Notify deleted participant to remove chat
            if ($participant->user_id === $participant_user_id) {
                CreateNotification::run(['type' => Notification::TYPE_DELETED_CHAT] + $data, $participant->user);
            } else {
                // Notify the rest of the participants that a participant has been removed.
                CreateNotification::run($data, $participant->user);
            }
        });

        return true;
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('message.delete', ChatParticipant::class);
    }

    public function asController(ActionRequest $_, ChatParticipant $chat_participant): bool
    {
        return $this->handle($chat_participant);
    }
}
