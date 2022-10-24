<?php

namespace App\Domain\Chat\Actions;

use App\Domain\Chat\Aggregates\ChatMessageAggregate;
use App\Domain\Chat\Models\ChatMessage;
use App\Http\Middleware\InjectClientId;
use Illuminate\Console\Command;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\VarDumper\VarDumper;

class DeleteMessage
{
    use AsAction;

    public string $commandSignature = 'chat:delete-message';

    public function handle(ChatMessage $message): ChatMessage
    {
        ChatMessageAggregate::retrieve($message->id)->delete()->persist();

        return $message;
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('message.delete', ChatMessage::class);
    }

    public function asController(ActionRequest $request): ChatMessage
    {
        $data = $request->validated();
        $data['client_id'] = $request->user()->client_id;

        return $this->handle(
            $data,
            $request->user(),
        );
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
