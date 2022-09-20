<?php

declare(strict_types=1);

namespace App\Domain\Conversations\Twilio\Actions;

use App\Domain\Users\Models\User;
use Illuminate\Console\Command;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Silber\Bouncer\BouncerFacade as Bouncer;

class AddConversationAgent
{
    use AsAction;
    public const CHAT_CONVERSATION_ABILITY_NAME = 'conversation.read';

    public string $commandSignature = 'conversation:agent {user?*}';
    public string $commandDescription = 'Add user to conversation agents.';

    public function handle(User $user): void
    {
        Bouncer::allow($user)->to(self::CHAT_CONVERSATION_ABILITY_NAME);
    }

    public function asController(ActionRequest $request)
    {
    }

    public function asCommand(Command $command): void
    {
        foreach ($command->argument('user') as $user_id) {
            $this->handle($user = User::find($user_id));
            $command->info("{$user->first_name} {$user->last_name} is now a chat agent!");
        }
    }
}
