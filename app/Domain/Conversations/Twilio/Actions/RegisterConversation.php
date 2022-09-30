<?php

declare(strict_types=1);

namespace App\Domain\Conversations\Twilio\Actions;

use App\Domain\Clients\Projections\Client;
use App\Domain\Conversations\Twilio\Exceptions\ConversationException;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;

class RegisterConversation
{
    use AsAction;
    public string $commandSignature = 'conversation:register';
    public string $commandDescription = 'Sets up conversation for specified clients.';

    /**
     * @param Client      $client
     * @param string|null $conversation_sid
     *
     * @return void
     * @throws ConfigurationException
     * @throws ConversationException
     * @throws TwilioException
     */
    public function handle(Client $client, ?string $conversation_sid): void
    {
        $client->getTwilioService()->createConversationForClient($conversation_sid);
    }

    /**
     * @param Command $command
     *
     * @return void
     * @throws ConfigurationException
     * @throws ConversationException
     * @throws TwilioException
     */
    public function asCommand(Command $command): void
    {
        $name = $command->choice('Select Client', Client::all()->pluck('name')->toArray());
        $sid = $command->ask('Enter Conversation SID (Press Enter if you dont have one)');

        $client = Client::where(['name' => $name])->first();
        $this->handle($client, $sid);
        $command->info("Conversation has been initialized for '{$client->name}'");
        $command->warn('Dont forget to enable "Autocreate a Conversation" for Default Messaging Service for Conversations/Integration');
    }
}
