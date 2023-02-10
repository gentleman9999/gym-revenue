<?php

declare(strict_types=1);

namespace App\Domain\Chat\Actions;

use App\Domain\Chat\Aggregates\ChatAggregate;
use App\Domain\Chat\Models\Chat;
use App\Domain\Users\Models\User;
use App\Http\Middleware\InjectClientId;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateChat
{
    use AsAction;

    public string $commandSignature = 'chat:update-chat';

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'client_id' => ['required', 'string'],
            'participant_ids' => ['sometimes', 'string'],
        ];
    }

    public function handle(array $payload)
    {
        $aggy = ChatAggregate::retrieve($payload['id']);

        $aggy->update($payload)->persist();
        $chat = Chat::findOrFail($payload['id']);

        return $chat->refresh();
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

    public function asController(ActionRequest $request, Chat $chat): Chat
    {
        $data = $request->validated();

        return $this->handle(
            $data,
            $chat,
        );
    }

    public function asCommand(Command $command): void
    {
        $payload       = [];
        $chat          = Chat::find($command->choice(
            'Select Chat Session you want to Update:',
            Chat::all()->pluck('id')->toArray(),
        ));
        $payload['id'] = $chat->id;
        $updateField   = $command->choice(
            'What field do you want to change?',
            Schema::getColumnListing($chat->getTable())
        );
        $client_id     = Chat::whereId($chat->id)->pluck('client_id')->toArray()[0];
        $users         = User::whereClientId($client_id)->pluck('id')->toArray();
        switch ($updateField) {
            case 'participant_ids':
                $payload[$updateField] = json_encode($command->choice(
                    'Select who are participants in this chat (comma separated) ' . $chat->participant_ids,
                    $users,
                    multiple: true,
                ));
                $this->handle($payload);

                break;
            case 'created_by_user_id':
                $payload[$updateField] = $command->choice(
                    'Select who created this chat[' . $chat->created_by_user_id . ']',
                    $users,
                );
                $this->handle($payload);

                break;
            default:
                $command->info('Cannot update this field ' . $updateField);

                break;
        }
    }
}
