<?php

declare(strict_types=1);

namespace App\Domain\Chat\Actions;

use App\Domain\Chat\Aggregates\ChatMessageAggregate;
use App\Domain\Chat\Models\ChatMessage;
use App\Domain\Chat\Models\ChatParticipant;
use App\Domain\Notifications\Actions\CreateNotification;
use App\Domain\Notifications\Notification;
use App\Http\Middleware\InjectClientId;
use Illuminate\Console\Command;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\VarDumper\VarDumper;

class DeleteMessage
{
    use AsAction;

    public string $commandSignature = 'chat:delete-message';

    public function handle(ChatMessage $chat_message): bool
    {
        $participants = $chat_message->chat->participants;
        $data         = [
            'chat_id' => $chat_message->chat_id,
            'message' => $chat_message,
            'type' => Notification::TYPE_DELETED_CHAT_MESSAGE,
        ];

        ChatMessageAggregate::retrieve($chat_message->id)->delete()->persist();

        $participants->each(static fn (ChatParticipant $p) => CreateNotification::run($data, $p->user));

        return true;
    }

    /**
     * @return string[]
     */
    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('message.delete', ChatMessage::class);
    }

    public function asController(ActionRequest $request, ChatMessage $chat_message): bool
    {
        return $this->handle($chat_message);
    }

    public function asCommand(Command $command): void
    {
        $payload = [];
        $payload = ChatMessage::find($command->choice(
            'Select Message to Delete:',
            ChatMessage::all()->pluck('id')->toArray(),
        ));
        VarDumper::dump($payload);
        $this->handle($payload);
    }
}
