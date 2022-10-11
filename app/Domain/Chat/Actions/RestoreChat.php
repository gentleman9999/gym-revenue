<?php

namespace App\Domain\Chat\Actions;

use App\Domain\Chat\Aggregates\ChatAggregate;
use App\Domain\Chat\Models\Chat;
use App\Http\Middleware\InjectClientId;
use Illuminate\Console\Command;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\VarDumper\VarDumper;

class RestoreChat
{
    use AsAction;

    public string $commandSignature = 'chat:restore-chat';

    public function handle(Chat $chat): Chat
    {
        ChatAggregate::retrieve($chat->id)->restore()->persist();

        return $chat->refresh();
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('chat.restore', Chat::class);
    }

    public function asController(Chat $chat): Chat
    {
        return $this->handle(
            $chat,
        );
    }

    public function asCommand(Command $command): void
    {
        $payload = Chat::onlyTrashed()->find($command->choice(
            'Select Chat Session you want to Restore:',
            Chat::onlyTrashed()->pluck('id')->toArray(),
        ));
        VarDumper::dump($payload);
        $this->handle($payload);
    }
}
