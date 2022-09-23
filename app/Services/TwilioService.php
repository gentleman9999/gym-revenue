<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Clients\Actions\UpdateGateways;
use App\Domain\Clients\Projections\Client;
use App\Domain\Conversations\Twilio\Exceptions\ConversationException;
use App\Domain\Users\Models\User;
use App\Models\Utility\AppState;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\DB;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\RestException;
use Twilio\Exceptions\TwilioException;
use Twilio\Jwt\AccessToken;
use Twilio\Rest\Client as TwilioClient;
use Twilio\Rest\Conversations\V1\Conversation\ParticipantInstance;
use TypeError;

class TwilioService
{
    public readonly TwilioClient $twilio;
    public readonly string       $sid;
    public readonly string       $token;
    public readonly string       $number;
    public readonly ?string      $api_key;
    public readonly ?string      $api_secret;
    public readonly ?string      $conversation_service_sid;
    private static array         $instances = [];

    /**
     * Creates Twilio client instance from gateway config.
     *
     * @throws ConfigurationException
     * @throws ConversationException
     */
    protected function __construct(public Client $client)
    {
        $settings = $client->getNamedGatewaySettings();
        if (AppState::isSimuationMode()) {
            $settings += [
                'twilioNumber' => env('TWILIO_NO'),
                'twilioSID' => env('TWILIO_SID'),
                'twilioToken' => env('TWILIO_TOKEN'),
                'twilioApiKey' => env('TWILIO_API_KEY'),
                'twilioApiSecret' => env('TWILIO_API_SECRET'),
            ];
        }

        // Required Fields
        isset($settings['twilioSID']) || throw new ConversationException('"twilioSID" is required');
        isset($settings['twilioToken']) || throw new ConversationException('"twilioToken" is required');
        isset($settings['twilioNumber']) || throw new ConversationException('"twilioNumber" is required');

        $this->twilio = new TwilioClient($this->sid = $settings['twilioSID'], $this->token = $settings['twilioToken']);
        $this->number = $settings['twilioNumber'];
        $this->api_key = $settings['twilioApiKey'];
        $this->api_secret = $settings['twilioApiSecret'];
        $this->conversation_service_sid = $settings['twilioConversationServiceSID'] ?? null;
    }

    /**
     * @throws ConfigurationException
     * @throws ConversationException
     */
    public static function get(Client $client): static
    {
        return static::$instances[$client->id] ?? static::$instances[$client->id] = new static($client);
    }

    /**
     * Get a front-end access token for specified grants
     *
     * @param User  $user
     * @param array $grants
     * @param int   $ttl
     *
     * @return array
     * @example getAccessToken($user, [(new ChatGrant())->setServiceSid(serviceSid), ...])
     */
    public function getAccessToken(User $user, array $grants, int $ttl = 3600): array
    {
        $identity = "{$user->first_name} {$user->last_name}";
        $token = new AccessToken($this->sid, $this->api_key, $this->api_secret, $ttl, $identity);
        array_map([$token, 'addGrant'], $grants);

        return ['token' => $token->toJWT(), 'identity' => $identity];
    }

    /**
     *
     * @param string|null $conversation_sid If conversation already exist, we can pass the conversation id, else,
     *                                      a new one is created.
     *
     * @return void
     * @throws TwilioException
     */
    public function createConversationForClient(?string $conversation_sid): void
    {
        DB::transaction(function () use ($conversation_sid): void {
            $conversation = $this->twilio->conversations->v1;
            $client_id = $this->client->id;
            $host = Env::get('EXPOSED_APP_URL', Env::get('APP_URL'));

            try {
                // Check if service already exist.
                $conversation_sid = $conversation->services($this->conversation_service_sid)->fetch()->sid;
            } catch (RestException|TypeError) {
                $conversation_sid ??= $conversation->services->create('Chat service')->sid;
                UpdateGateways::run(['client_id' => $client_id, 'twilioConversationServiceSID' => $conversation_sid]);
            }

            // https://www.twilio.com/docs/conversations/conversations-webhooks#webhook-action-triggers
            $conversation->services($conversation_sid)->configuration->webhooks()->update([
                // https://www.twilio.com/docs/conversations/conversations-webhooks#webhook-action-triggers
                'filters' => ['onConversationAdded', 'onMessageAdded'],
                'method' => 'POST',
                'postWebhookUrl' => "{$host}/api/twilio/conversation/{$client_id}",
            ]);

            // Service as default conversation
            $conversation->configuration()->update(['defaultChatServiceSid' => $conversation_sid]);
        });
    }

    /**
     * Adds a user to a conversation
     *
     * @param string $conversation_sid
     * @param User   $user
     *
     * @return ParticipantInstance
     * @throws TwilioException
     */
    public function addParticipantToConversation(string $conversation_sid, User $user): ParticipantInstance
    {
        $role_name = 'conversation-agent';
        $role = null;

        // check if we already have this role.
        foreach ($this->twilio->conversations->v1->roles->read() as $role_instance) {
            if ($role_instance->friendlyName === $role_name) {
                $role = $role_instance;

                break;
            }
        }

        $role ??= $this->twilio->conversations->v1->roles->create($role_name, 'conversation', [
            'addParticipant',
            'deleteAnyMessage',
            'addNonChatParticipant',
            'removeParticipant',
            'editNotificationLevel',
            'deleteOwnMessage',
            'deleteConversation',
            'editAnyParticipantAttributes',
            'editAnyMessage',
            'editAnyMessageAttributes',
            'editConversationAttributes',
            'editConversationName',
            'editOwnMessage',
            'editOwnMessageAttributes',
            'leaveConversation',
            'sendMediaMessage',
            'sendMessage',
            'editOwnParticipantAttributes',
        ]);

        return $this->twilio->conversations->v1->conversations($conversation_sid)->participants->create([
            'identity' => "{$user->first_name} {$user->last_name}" ,
            'roleSid' => $role->sid,
        ]);
    }

    public function setIdentityForParticipant(string $identity, string $participant_sid, string $conversation_sid)
    {
        $this->twilio->conversations->v1->conversations($conversation_sid)
            ->participants($participant_sid)
            ->update(['attributes' => json_encode(['identity' => $identity])]);
    }

    public function deleteService()
    {
        // $conversation->services('ISd936515f10bb49d7bde01d510219ccbb')->delete();
    }
}
