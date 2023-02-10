<?php

declare(strict_types=1);

namespace App\Domain\Chat\Actions;

use App\Domain\Chat\Aggregates\ChatAggregate;
use App\Domain\Chat\Models\Chat;
use App\Domain\Clients\Projections\Client;
use App\Domain\Users\Models\User;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Illuminate\Console\Command;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateChat
{
    use AsAction;

    public string $commandSignature = 'chat:create-chat';

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            // int[]
            'participant_ids' => ['required', 'array'],
        ];
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return Chat
     */
    public function handle(array $payload): Chat
    {
        // Add current user as participant
        $payload['participant_ids'][] = $payload['user_id'];

        // Check if chat exist for and get its instance instead of creating a new one.
        $id = Chat::findChatForUsers($payload['participant_ids']);
        if ($id === null) {
            $id = Uuid::get();
            ChatAggregate::retrieve($id)->create($payload)->persist();
        }

        return Chat::with(['participants.user', 'messages'])->findOrFail($id);
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
        return $request->user()->can('chat.create', Chat::class);
    }

    public function asController(ActionRequest $request): Chat
    {
        /** @var User $user */
        $user = $request->user();

        return $this->handle($request->validated() + [
            'user_id'   => $user->id,
            'client_id' => $user->client_id,
        ]);
    }

    public function asCommand(Command $command): void
    {
        $payload     = [];
        $client_name = $command->choice('Select Client (comma separated multiple options)', Client::all()
            ->pluck('name')
            ->toArray());

        $payload['client_id']          = Client::whereName($client_name)->pluck('id')->toArray()[0];
        $users                         = User::whereClientId(
            Client::whereName($client_name)->pluck('id')->toArray()
        )->pluck('id')->toArray();
        $creating_user                 = $command->choice('Select who you are', $users,);
        $payload['created_by_user_id'] = $creating_user;
        $participating_users           = $command->choice('Select who you want to chat with', $users, multiple: true);
        $participating_users[]         = $creating_user;
        $payload['participant_ids']    = json_encode($participating_users);

        $message = $this->handle($payload);
        $command->info($message);
    }
}
