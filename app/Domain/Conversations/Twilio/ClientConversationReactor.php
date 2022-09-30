<?php

declare(strict_types=1);

namespace App\Domain\Conversations\Twilio;

use App\Domain\Clients\Projections\Client;
use App\Domain\Conversations\Twilio\Events\ClientConversationCreated;
use App\Domain\Conversations\Twilio\Events\ClientConversationJoined;
use App\Domain\Conversations\Twilio\Exceptions\ConversationException;
use App\Domain\Conversations\Twilio\Models\ClientConversation;
use App\Domain\EndUsers\Projections\EndUser;
use App\Domain\Notifications\Actions\CreateNotification;
use App\Domain\Notifications\Notification;
use App\Domain\Users\Models\User;
use App\Models\GatewayProviders\GatewayProvider;
use App\Models\Utility\AppState;
use Illuminate\Support\Env;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\RestException;
use Twilio\Exceptions\TwilioException;

class ClientConversationReactor extends Reactor
{
    /**
     * @param ClientConversationJoined $event
     *
     * @return void
     * @throws Exceptions\ConversationException
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public function onClientConversationJoined(ClientConversationJoined $event): void
    {
        /** @var Client $client */
        $client = Client::find($event->clientId());
        /** @var ClientConversation $conversation */
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
            $identity = $event->payload['sender'];

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
        } else {
            $user = $conversation->user;
        }

        CreateNotification::run([
            'text' => "You have a new message from {$event->payload['sender']}",
            'type' => Notification::TYPE_NEW_CONVERSATION,
        ], $user);
    }

    /**
     * @param ClientConversationCreated $event
     *
     * @return void
     * @throws Exceptions\ConversationException
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public function onClientConversationCreated(ClientConversationCreated $event): void
    {
        [$end_user_type, $id] = $event->payload['end_user'];
        /** @var EndUser $end_user */
        $end_user = $end_user_type::find($id);
        /** @var User $user */
        $user = User::find($event->payload['user_id']);

        $client = $user->client;
        $twilio_service = $client->getTwilioService();
        $phone = $end_user->getPhoneNumber() ?: throw new ConversationException('End user has no phone number');
        $gateway_provider_id = $client->getGatewayProviderBySlug(GatewayProvider::PROVIDER_SLUG_TWILIO_CONVERSION)->id;

        if (AppState::isSimuationMode()) {
            $phone = Env::get('TWILIO_TEST_EMPLOYEE_NO') ?: throw new ConversationException(
                'For development env.TWILIO_TEST_EMPLOYEE_NO is required.'
            );
        }

        // We need to use the current lead/members phone as name, so we want to strip out anything that is not a number
        $conversation_uid = preg_replace('/[^\d]/', '', $phone);

        // Get or creat (if not exist) conversation.
        try {
            $conversation_id = $twilio_service->twilio->conversations->v1->conversations($conversation_uid)->fetch()->sid;
        } catch (RestException) {
            $conversation_id = $twilio_service->twilio->conversations->v1->conversations->create([
                'uniqueName' => $conversation_uid,
            ])->sid;

            // Add the lead/member to conversation.
            $twilio_service->twilio->conversations->v1->conversations($conversation_id)->participants->create([
                "messagingBindingAddress" => $twilio_service->formatNumber($phone),
                "messagingBindingProxyAddress" => $twilio_service->number,
                'attributes' => json_encode(['identity' => $phone]),
            ]);
        }

        // Add current user to conversation and store instance.
        if (! ClientConversation::where(['conversation_id' => $conversation_id, 'user_id' => $user->id])->exists()) {
            ClientConversation::create($event->payload + [
                'id' => $event->aggregateRootUuid(),
                'user_conversation_id' => $twilio_service->addParticipantToConversation($conversation_id, $user)->sid,
                'conversation_id' => $conversation_id,
                'gateway_provider_id' => $gateway_provider_id,
            ]);
        }
    }
}
