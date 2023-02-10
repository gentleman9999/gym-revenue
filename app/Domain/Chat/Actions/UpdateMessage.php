<?php

declare(strict_types=1);

namespace App\Domain\Chat\Actions;

use App\Domain\Chat\Aggregates\ChatMessageAggregate;
use App\Domain\Chat\Models\Chat;
use App\Domain\Chat\Models\ChatMessage;
use App\Domain\Chat\Models\ChatParticipant;
use App\Domain\Notifications\Actions\CreateNotification;
use App\Domain\Notifications\Notification;
use App\Domain\Users\Models\User;
use App\Http\Middleware\InjectClientId;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\VarDumper\VarDumper;

class UpdateMessage
{
    use AsAction;

    public string $commandSignature = 'chat:update-message';

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'message' => ['sometimes', 'string'],
            'read_by' => ['sometimes', 'array'], // array of participant ids
        ];
    }

    public function handle(array $data, ChatMessage $chat_message): ChatMessage
    {
        ChatMessageAggregate::retrieve($chat_message->id)->update($data)->persist();
        $chat_message->refresh();

        $data = [
            'chat_id' => $chat_message->chat_id,
            'message' => $chat_message,
            'type' => Notification::TYPE_UPDATED_CHAT_MESSAGE,
        ];

        $chat_message->chat->participants->each(
            static fn (ChatParticipant $p) => CreateNotification::run($data, $p->user)
        );


        return $chat_message;
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

        return $current_user->can('chat.update', Chat::class);
    }

    public function asController(ActionRequest $request, ChatMessage $chat_message): ChatMessage
    {
        return $this->handle($request->validated(), $chat_message);
    }

    public function asCommand(Command $command): void
    {
        $payload                     = [];
        $chat                        = Chat::find($command->choice(
            'Select Chat Session you want to Update:',
            Chat::all()->pluck('id')->toArray(),
        ));
        $payload['internal_chat_id'] = $chat->id;
        $chatDetail                  = ChatMessage::find($command->choice(
            'What message do you want to change?',
            ChatMessage::all()->pluck('id')->toArray(),
        ));
        VarDumper::dump($chatDetail);
        $payload['id'] = $chatDetail->id;
        $updateField   = $command->choice(
            'What field do you want to change?',
            Schema::getColumnListing($chatDetail->getTable())
        );
        $client_id     = Chat::whereId($chat->id)->pluck('client_id')->toArray()[0];
        $users         = User::whereClientId($client_id)->pluck('id')->toArray();
        switch ($updateField) {
            case 'field':
                $payload[$updateField] = $command->ask('What would you like to say? [' . $chatDetail->field . ']');
                $this->handle($payload, $chatDetail);

                break;
            case 'value':
                $payload[$updateField] = $command->ask('What would you like to say? [' . $chatDetail->value . ']');
                $this->handle($payload, $chatDetail);

                break;
            case 'misc':
                $payload[$updateField] = $command->choice(
                    'Select who created this message [' . $chatDetail->misc . ']',
                    $users,
                );
                $this->handle($payload, $chatDetail);

                break;
            default:
                $command->info('Cannot update this field ' . $updateField);

                break;
        }
    }
}
