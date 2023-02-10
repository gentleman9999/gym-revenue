<?php

declare(strict_types=1);

namespace App\Services\GatewayProviders\Voice;

use App\Domain\Clients\Models\ClientGatewayIntegration;
use App\Domain\Users\Models\User;
use App\Models\GatewayProviders\GatewayProviderType;
use App\Services\GatewayProviders\Exceptions\GatewayProviderException;
use App\Services\GatewayProviders\GatewayProviderService;
use App\Services\GatewayProviders\Profiles\Voice\TwilioVoice;
use App\Services\GatewayProviders\Profiles\Voice\VoiceGatewayProvider;
use RuntimeException;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Rest\Api\V2010\Account\CallInstance;

class VoiceGatewayProviderService extends GatewayProviderService
{
    protected string $provider_type_slug = 'voice';
    protected User $caller;
    protected VoiceGatewayProvider $gateway;

    public function __construct()
    {
        $model = GatewayProviderType::where('name', '=', $this->provider_type_slug)->first();
        if ($model === null) {
            throw new RuntimeException("'{$this->provider_type_slug}' gateway provider not found, did you seed?");
        }

        parent::__construct($model);
    }

    /**
     *
     * @return $this
     * @throws ConfigurationException
     * @throws GatewayProviderException
     */
    public function initVoiceGateway(User $caller): self
    {
        $this->caller = $caller;
        $this->setAssociatedClient($caller->client_id);
        if ($gateway = $this->getVoiceGateway()) {
            $this->gateway = $gateway;
        }

        return $this;
    }

    public function call(string $to, bool $record = false): CallInstance
    {
        return $this->gateway->initializeCall($this->caller, $to, $record);
    }

    public function getCallStatus(string $sid): CallInstance
    {
        return $this->gateway->getCallStatus($sid);
    }

    /**
     * @throws ConfigurationException
     */
    private function getVoiceGateway(): ?TwilioVoice
    {
        $model = ClientGatewayIntegration::whereClientId($this->client->id ?? null)
            ->whereGatewayId(GatewayProviderType::whereName($this->provider_type_slug)->first()->id)
            ->first();

        //can't use default because that requires client_id
        $provider = $model === null ? 'default_cnb' : $model->value;

        if ($provider === 'default_cnb') {
            return new TwilioVoice([
                'twilio_no' => env('TWILIO_NO'),
                'twilio_sid' => env('TWILIO_SID'),
                'twilio_token' => env('TWILIO_TOKEN'),
            ]);
        }

        return null;
    }
}
