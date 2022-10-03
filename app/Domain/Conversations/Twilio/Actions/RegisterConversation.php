<?php

declare(strict_types=1);

namespace App\Domain\Conversations\Twilio\Actions;

use App\Domain\Clients\Models\ClientGatewaySetting;
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
     * @param string|null $messenger_id
     *
     * @return void
     * @throws ConfigurationException
     * @throws ConversationException
     * @throws TwilioException
     */
    public function handle(Client $client, ?string $messenger_id): void
    {
        $client->getTwilioService()->createConversationForClient($messenger_id);
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

        /** @var Client $client */
        $client = Client::where(['name' => $name])->first();
        $messenger_id = $client->getNamedGatewaySettings()[ClientGatewaySetting::NAME_TWILIO_MESSENGER_ID] ?? null;
        // Ask for new messenger ID if not was found on client setting.
        if ($messenger_id === null) {
            $messenger_id = $command->ask('Enter Messenger ID. Press Enter to disable messenger conversation');
        }

        $this->handle($client, $messenger_id);
        $command->info("Conversation has been initialized for '{$client->name}'");
    }
}
