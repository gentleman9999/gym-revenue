<?php

declare(strict_types=1);

namespace App\Domain\Conversations\Twilio;

use App\Domain\Clients\Projections\Client;
use App\Domain\Conversations\Twilio\Events\ClientConversationCreated;
use App\Domain\Conversations\Twilio\Models\ClientConversation;
use App\Models\GatewayProviders\GatewayProvider;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;

class ClientConversationReactor extends Reactor
{
    /**
     * @param ClientConversationCreated $event
     *
     * @return void
     * @throws Exceptions\ConversationException
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public function onTwilioClientConversationCreated(ClientConversationCreated $event): void
    {
        /** @var Client $client */
        $client = Client::find($event->clientId());
        $conversation = ClientConversation::where($payload = [
            'conversation_id' => $event->payload['conversation_id'],
            'gateway_provider_id' => $client->getGatewayProviderBySlug(GatewayProvider::PROVIDER_SLUG_TWILIO_CONVERSION)->id,
        ])->first();

        if ($conversation === null) {
            // Add an employee to conversation.
            // Find free employee with conversation agent permission
            $user = $client->getNextFreeConversationAgent();
            $found_free_agent = $user !== null;

            if ($found_free_agent) {
                $payload['user_id'] = $user->id;
            }

            $twilio_service = $client->getTwilioService();
            $conversation_sid = $event->payload['conversation_id'];
            $identity = $event->payload['author'];

            if ($found_free_agent) {
                $payload['user_conversation_id'] = $twilio_service->addParticipantToConversation($conversation_sid, $user)->sid;
//                $payload['user_conversation_id'] = 'MB64fc29886be8424c9515c8d13bb3b7a8';
            }

            // Even when no free agent is found, we still want to create this conversation, and since it has no user
            // attached to it, it can serve as a queue.
            ClientConversation::create($payload + ['id' => $event->aggregateRootUuid()]);

            // create identity for the lead/member that started conversation.
            // we don't care about identity if we are the one initializing conversation.
            if ($identity !== null) {
                $twilio_service->setIdentityForParticipant($identity, $event->payload['participant_id'], $conversation_sid);
            }
        }
    }
}
