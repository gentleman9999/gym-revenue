<?php

declare(strict_types=1);

namespace App\Services\GatewayProviders\Profiles\Voice;

use App\Domain\Users\Models\User;
use App\Domain\VoiceCalls\VoiceCallException;
use App\Models\Utility\AppState;
use Illuminate\Support\Env;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Api\V2010\Account\CallInstance;
use Twilio\Rest\Client as TwilioClient;

class TwilioVoice extends VoiceGatewayProvider
{
    protected TwilioClient $twilio_client;

    /**
     * @param array<string, mixed> $credentials
     *
     * @throws ConfigurationException
     */
    public function __construct(protected array $credentials)
    {
        parent::__construct($credentials, 'Twilio Voice');
        $this->twilio_client = new TwilioClient($credentials['twilio_sid'], $credentials['twilio_token']);
    }

    /**
     *
     * @throws TwilioException
     * @throws VoiceCallException
     */
    public function initializeCall(User $caller, string $to, bool $record = false): CallInstance
    {
        if (! ($from = $caller->phone)) {
            throw new VoiceCallException('User does not have a phone number set');
        }

        if (AppState::isSimuationMode()) {
            $from = Env::get('TWILIO_TEST_EMPLOYEE_NO');
            $to   = Env::get('TWILIO_TEST_LEAD_NO');
        }

        $to   = $this->twilio_client->lookups->v1->phoneNumbers($to)->fetch(['countryCode' => 'US'])->phoneNumber;
        $from = $this->twilio_client->lookups->v1->phoneNumbers($from)->fetch(['countryCode' => 'US'])->phoneNumber;
        $host = Env::get('EXPOSED_APP_URL', Env::get('APP_URL'));

        // https://www.twilio.com/docs/voice/api/call-resource#create-a-call-resource
        return $this->twilio_client->calls->create($from, Env::get('TWILIO_NO'), [
            'url' => "{$host}/api/twilio/call/connect/{$to}",
            'statusCallback' => "{$host}/api/twilio/call/status/{$caller->id}/provider/{$this->getGatewayProvider()->id}",
            // https://www.twilio.com/docs/voice/make-calls#get-call-status-events-during-a-call
            'statusCallbackEvent' => ['initiated', 'ringing', 'answered', 'completed'],
            // https://www.twilio.com/docs/voice/answering-machine-detection#asyncamd
            'MachineDetection' => 'Enable',
            'Record' => $record,
            'statusCallbackMethod' => 'POST',
        ]);
    }

    /**
     * @throws TwilioException
     */
    public function getCallStatus(string $sid): CallInstance
    {
        return $this->twilio_client->calls($sid)->fetch();
    }
}
