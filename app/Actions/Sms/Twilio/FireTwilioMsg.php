<?php

declare(strict_types=1);

namespace App\Actions\Sms\Twilio;

use App\Domain\Templates\Services\TemplateParserService;
use App\Domain\Users\Models\User;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Action;
use Lorisleiva\Actions\Concerns\AsAction;
use Twilio\Rest\Api\V2010\Account\MessageInstance;

class FireTwilioMsg extends Action
{
    use AsAction;
    public string $commandSignature = 'sms-test {phone}';

    public function handle(User $user, string $message): MessageInstance
    {
        return $user->client->getTwilioService()
            ->sendMessage($user, (new TemplateParserService($message))->swapTokens(['user' => $user]));
    }

    public function asCommand(Command $command): void
    {
        $user = User::wherePhone($command->argument('phone'))->firstOrFail();

        $this->handle($user, '{{user.first_name}} {{ user.last_name}} {{test.name}}');
    }
}
