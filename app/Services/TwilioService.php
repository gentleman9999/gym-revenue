<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Clients\Actions\UpdateGateways;
use App\Domain\Clients\Models\ClientGatewaySetting;
use App\Domain\Clients\Projections\Client;
use App\Domain\Users\Models\User;
use App\Interfaces\PhoneInterface;
use App\Services\Abstractions\AbstractInstanceCache;
use Illuminate\Support\Env;
use InvalidArgumentException;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\RestException;
use Twilio\Exceptions\TwilioException;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\Grant;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Client as TwilioClient;
use Twilio\Rest\Conversations\V1\Conversation\ParticipantInstance;
use TypeError;

/**
 *
 * Error code ref
 * https://www.twilio.com/docs/api/errors
 */
class TwilioService extends AbstractInstanceCache
{
    public readonly TwilioClient $twilio;
    public readonly string $sid;
    public readonly string $token;
    public readonly string $number;
    public readonly ?string $api_key;
    public readonly ?string $api_secret;
    public readonly ?string $conversation_service_sid;
    public readonly ?string $messenger_id;

    /**
     * Creates Twilio client instance from gateway config.
     *
     *
     * @throws ConfigurationException
     */
    protected function __construct(public Client $client)
    {
        $settings = $client->getNamedGatewaySettings();
        // Required Fields
        if (count($settings) > 0) {
            isset($settings['twilioSID']) || throw new InvalidArgumentException('"twilioSID" is required');
            isset($settings['twilioToken']) || throw new InvalidArgumentException('"twilioToken" is required');
            isset($settings['twilioNumber']) || throw new InvalidArgumentException('"twilioNumber" is required');
        } else {
            $settings['twilioSID']       = env('TWILIO_SID');
            $settings['twilioToken']     = env('TWILIO_TOKEN');
            $settings['twilioNumber']    = env('TWILIO_NO');
            $settings['twilioApiKey']    = env('TWILIO_API_KEY');
            $settings['twilioApiSecret'] = env('TWILIO_API_SECRET');
        }
        $this->client                   = $client;
        $this->twilio                   = new TwilioClient($this->sid = $settings['twilioSID'], $this->token = $settings['twilioToken']);
        $this->number                   = $settings[ClientGatewaySetting::NAME_TWILIO_NUMBER];
        $this->api_key                  = $settings[ClientGatewaySetting::NAME_TWILIO_API_KEY];
        $this->api_secret               = $settings[ClientGatewaySetting::NAME_TWILIO_API_SECRET];
        $this->conversation_service_sid = $settings[ClientGatewaySetting::NAME_TWILIO_CONVERSATION_SERVICES_ID] ?? null;
        $this->messenger_id             = $settings[ClientGatewaySetting::NAME_TWILIO_MESSENGER_ID] ?? null;
    }

    /**
     * Get a front-end access token for specified grants
     *
     * @param array<Grant> $grants
     *
     * @return array
     * @example getAccessToken($user, [(new ChatGrant())->setServiceSid(serviceSid), ...])
     */
    public function getAccessToken(User $user, array $grants, int $ttl = 3600): array
    {
        $identity = $user->name;
        $token    = new AccessToken($this->sid, $this->api_key, $this->api_secret, $ttl, $identity);
        array_map([$token, 'addGrant'], $grants);

        return ['token' => $token->toJWT(), 'identity' => $identity];
    }

    /**
     *
     * @param string|null $messenger_id Facebook messenger ID
     *
     * @throws TwilioException
     */
    public function createConversationForClient(?string $messenger_id): void
    {
        $conversation = $this->twilio->conversations->v1;
        $message      = $this->twilio->messaging->v1;
        $client_id    = $this->client->id;
        $host         = Env::get('EXPOSED_APP_URL', Env::get('APP_URL'));
        $endpoint     = "{$host}/api/twilio/conversation/{$client_id}";

        // Delete existing address configurations for sms and messenger
        // https://www.twilio.com/docs/conversations/api/address-configuration-resource?code-sample=code-delete-address-configuration&code-language=PHP&code-sdk-version=6.x
        foreach ($conversation->addressConfigurations->read(['sms', 'messenger']) as $record) {
            $conversation->addressConfigurations($record->sid)->delete();
        }

        // Delete existing conversation services
        foreach ($conversation->services->read() as $record) {
            $conversation->services($record->sid)->delete();
        }

        // https://www.twilio.com/docs/conversations/api/service-resource#create-a-service-resource
        $conversation_sid ??= $conversation->services->create('conversation-service')->sid;

        $message_sid          = null;
        $message_service_name = 'conversation-message-service';
        // Check if message service exist
        foreach ($message->services->read() as $record) {
            if ($record->friendlyName === $message_service_name) {
                $message_sid = $record->sid;

                break;
            }
        }

        // https://www.twilio.com/docs/messaging/services/api#create-a-service-resource
        $message_sid ??= $message->services->create($message_service_name)->sid;

        $this->updateOrCreateAddressConfig('sms', $this->number, 'sms-address-config', $conversation_sid, $endpoint);
        $this->updateOrCreateAddressConfig('messenger', $this->formatMessengerId($messenger_id ?? $this->messenger_id), 'messenger-address-config', $conversation_sid, $endpoint);

        // Service as default conversation
        // https://www.twilio.com/docs/conversations/api/configuration-resource?code-sample=code-update-configuration&code-language=PHP&code-sdk-version=6.x
        $conversation->configuration()->update([
            'defaultChatServiceSid' => $conversation_sid,
            'defaultMessagingServiceSid' => $message_sid,
        ]);

        // Save conversation and messenger id if provided in the database.
        UpdateGateways::run([
            ClientGatewaySetting::NAME_CLIENT_ID => $client_id,
            ClientGatewaySetting::NAME_TWILIO_CONVERSATION_SERVICES_ID => $conversation_sid,
            ClientGatewaySetting::NAME_TWILIO_MESSENGER_ID => $messenger_id,
        ]);
    }

    /**
     * Adds a user to a conversation
     *
     *
     * @throws TwilioException
     */
    public function addParticipantToConversation(string $conversation_sid, User $user): ParticipantInstance
    {
        $conversation = $this->twilio->conversations->v1;
        $role_name    = 'conversation-agent';
        $role         = null;

        // check if we already have this role.
        foreach ($conversation->roles->read() as $role_instance) {
            if ($role_instance->friendlyName === $role_name) {
                $role = $role_instance;

                break;
            }
        }

        $role ??= $conversation->roles->create($role_name, 'conversation', [
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

        return $conversation->conversations($conversation_sid)->participants->create([
            'identity' => $user->name,
            'roleSid' => $role->sid,
        ]);
    }

    public function setIdentityForParticipant(string $identity, string $participant_sid, string $conversation_sid): void
    {
        $this->twilio->conversations->v1->conversations($conversation_sid)
            ->participants($participant_sid)
            ->update(['attributes' => json_encode(['identity' => $identity])]);
    }

    public function formatNumber(string $number, string $country_code = 'US'): string
    {
        return $this->twilio->lookups->v1->phoneNumbers($number)->fetch(['countryCode' => $country_code])->phoneNumber;
    }

    /**
     * @param User   $user
     *
     * @throws TwilioException
     */
    public function sendMessage(PhoneInterface $user, string $message): MessageInstance
    {
        return $this->twilio->messages->create($this->formatNumber($user->getPhoneNumber()), [
            'from' => $this->number,
            'body' => $message,
            'statusCallBack' => env('TWILIO_STATUS_CALLBACK_DOMAIN') . '/api/twilio/statusCallBack',
        ]);
    }

    private function formatMessengerId(?string $id): ?string
    {
        return $id === null ? null : 'messenger:' . ltrim($id, 'messenger:');
    }

    /**
     * Adds sms, whatsapp and messenger auto conversation create.
     *
     * @param string      $type    Type of Address, value can be whatsapp, messenger or sms.
     * @param string|null $address The unique address to be configured. The address can be a
     *                             whatsapp address or phone number
     *
     * @throws TwilioException
     */
    private function updateOrCreateAddressConfig(
        string $type,
        ?string $address,
        string $name,
        string $conversation_sid,
        string $endpoint
    ): void {
        if ($address === null) {
            return;
        }

        $conversation = $this->twilio->conversations->v1;

        try {
            $sid = $conversation->addressConfigurations($address)->fetch()->sid;
        } catch (RestException | TypeError) {
            $sid = $conversation->addressConfigurations->create($type, $address)->sid;
        }

        // https://www.twilio.com/docs/conversations/api/address-configuration-resource?code-sample=code-update-address-configuration&code-language=PHP&code-sdk-version=6.x
        // https://www.twilio.com/docs/conversations/facebook-messenger#setting-up-conversation-autocreation
        // https://www.twilio.com/docs/conversations/api/address-configuration-resource#create-an-addressconfiguration-resource
        $conversation->addressConfigurations($sid)->update([
            'friendlyName' => $name,
            'autoCreationEnabled' => true,
            'autoCreationType' => 'webhook',
            'autoCreationConversationServiceSid' => $conversation_sid,
            'autoCreationWebhookUrl' => $endpoint,
            'autoCreationWebhookMethod' => 'POST',
            'autoCreationWebhookFilters' => ['onParticipantAdded', 'onMessageAdded'],
        ]);
    }
}
