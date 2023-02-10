<?php

declare(strict_types=1);

namespace App\Domain\Chat\Actions;

use App\Domain\Chat\Aggregates\ChatAggregate;
use App\Domain\Chat\Models\Chat;
use App\Http\Middleware\InjectClientId;
use Illuminate\Console\Command;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\VarDumper\VarDumper;

class DeleteChat
{
    use AsAction;

    public string $commandSignature = 'chat:delete-chat';

    public function handle(Chat $chat): Chat
    {
        VarDumper::dump($chat);
        ChatAggregate::retrieve($chat->id)->delete()->persist();

        return $chat;
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

        return $current_user->can('chat.delete', Chat::class);
    }

    public function asController(ActionRequest $request): Chat
    {
        $data              = $request->validated();
        $data['client_id'] = $request->user()->client_id;

        return $this->handle(
            $data,
            $request->user(),
        );
    }

    public function asCommand(Command $command): void
    {
        $payload = [];
        $payload = Chat::find($command->choice(
            'Select Chat Session to Delete:',
            Chat::all()->pluck('id')->toArray(),
        ));
        VarDumper::dump($payload);
        $this->handle($payload);
    }
}
