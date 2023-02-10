<?php

declare(strict_types=1);

namespace App\Domain\Chat\Actions;

use App\Domain\Chat\Aggregates\ChatMessageAggregate;
use App\Domain\Chat\Models\ChatMessage;
use App\Http\Middleware\InjectClientId;
use Illuminate\Console\Command;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\VarDumper\VarDumper;

class RestoreMessage
{
    use AsAction;

    public string $commandSignature = 'chat:restore-message';

    public function handle(ChatMessage $chat): ChatMessage
    {
        ChatMessageAggregate::retrieve($chat->id)->restore()->persist();

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

        return $current_user->can('message.restore', ChatMessage::class);
    }

    public function asController(ChatMessage $message): ChatMessage
    {
        return $this->handle(
            $message,
        );
    }

    public function asCommand(Command $command): void
    {
        $payload = [];
        $payload = ChatMessage::onlyTrashed()->find($command->choice(
            'Select Message to Restore:',
            ChatMessage::onlyTrashed()->pluck('id')->toArray(),
        ));
        VarDumper::dump($payload);
        $this->handle($payload);
    }
}
