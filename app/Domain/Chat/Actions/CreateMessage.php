<?php

declare(strict_types=1);

namespace App\Domain\Chat\Actions;

use App\Domain\Chat\Aggregates\ChatMessageAggregate;
use App\Domain\Chat\Models\Chat;
use App\Domain\Chat\Models\ChatMessage;
use App\Domain\Chat\Models\ChatParticipant;
use App\Domain\Notifications\Actions\CreateNotification;
use App\Domain\Notifications\Notification;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Illuminate\Console\Command;
use InvalidArgumentException;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateMessage
{
    use AsAction;

    public string $commandSignature = 'chat:create-message {--chat_id=} {--user_id=} {--message=}';

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'message' => ['required', 'string'],
            'chat_id' => ['required', 'string'],
        ];
    }

    /**
     * @param string $user_id
     * @param array<string, mixed>  $payload
     *
     */
    public function handle(string $user_id, array $payload): void
    {
        $id                             = Uuid::get();
        $payload['chat_participant_id'] = ChatParticipant::getIdForChatUser($payload['chat_id'], $user_id);
        ChatMessageAggregate::retrieve($id)->create($payload)->persist();

        /** @var Chat $chat */
        $chat = Chat::with('participants.user')->find($payload['chat_id']);
        $data = [
            'chat_id' => $payload['chat_id'],
            'participant_id' => $payload['chat_participant_id'],
            'message' => ChatMessage::find($id),
            'type' => Notification::TYPE_NEW_CHAT_MESSAGE,
        ];

        $chat->participants->each(static fn (ChatParticipant $p) => CreateNotification::run($data, $p->user));
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

        return $current_user->can('chat.create', Chat::class);
    }

    public function asController(ActionRequest $request): void
    {
        $this->handle($request->user()->id, $request->validated());
    }

    public function asCommand(Command $command): void
    {
        $user_id = $command->option('user_id') ?: throw new InvalidArgumentException('user_id is required');
        $chat_id = $command->option('chat_id') ?: throw new InvalidArgumentException('chat_id is required');
        $message = $command->option('message') ?: throw new InvalidArgumentException('message is required');

        // php artisan chat:create-message --chat_id=f0a7965e-9183-47dd-bc2c-687957293d33 --user_id=45 -message="Hello"
        $this->handle((int) $user_id, ['chat_id' => $chat_id, 'message' => $message,]);

        $command->info('Message sent');
    }
}
