<?php

declare(strict_types=1);

namespace App\Services\GatewayProviders\SMS;

use App\Domain\Clients\Models\ClientGatewayIntegration;
use App\Domain\Templates\SmsTemplates\Projections\SmsTemplate;
use App\Models\GatewayProviders\GatewayProvider;
use App\Models\GatewayProviders\GatewayProviderType;
use App\Services\GatewayProviders\GatewayProviderService;
use App\Services\GatewayProviders\Profiles\SMS\SMSGatewayProvider;
use App\Services\GatewayProviders\Profiles\SMS\TwilioSMS;

class SMSGatewayProviderService extends GatewayProviderService
{
    protected $provider_type_slug = 'sms';
    protected SmsTemplate $sms_template;
    protected SMSGatewayProvider $gateway;

    public function __construct(SmsTemplate $sms_template)
    {
        $this->sms_template = $sms_template;
        $model              = GatewayProviderType::where('name', '=', $this->provider_type_slug)->first();
        parent::__construct($model);
        if ($this->sms_template->client_id !== null) {
            $this->setAssociatedClient($this->sms_template->client_id);
        }
    }

    public function initSMSGateway($user_id): void
    {
        if ($gateway = $this->getSMSGateway($user_id)) {
            $this->gateway = $gateway;
        }
    }

    public function getRawMessage(): string
    {
        return $this->sms_template->markup;
    }

    public function getTranslatedMessage()
    {
        $results     = false;
        $raw_message = $this->getRawMessage();

        return $this->gateway->translateMessage($raw_message);
    }

    public function fire($phone_number)
    {
        $msg = $this->getRawMessage();
        return $this->gateway->fireMsg($phone_number, $msg);
    }

    public function fireBulk()
    {
        return false;
    }

    private function getSMSGateway($user_id)
    {
        $results  = false;
        $model    = $this->sms_template->gateway()->first();
        $provider = 'default_cnb'; //can't use default because that requires client_id

        if ($model !== null) {
            $provider = $model->value;
        }
        switch ($provider) {
            case 'default_cnb':
                $deets   = [
                    'twilio_no' => env('TWILIO_NO'),
                    'twilio_sid' => env('TWILIO_SID'),
                    'twilio_token' => env('TWILIO_TOKEN'),
                ];
                $results = new TwilioSMS($deets, $user_id);

                break;
                // default will be the slug name given to the
                // client_gateway_integrations configuration

            default:
                 $client_integration_record = ClientGatewayIntegration::whereClientId($this->client->id)
                    ->whereNickname($model->value)->whereActive(1)->first(); //This needs to find the correct gateway_slug, right now it doesn't

                if ($client_integration_record !== null) {
                    $gateway_provider_record = GatewayProvider::whereSlug($client_integration_record->gateway_slug)
                        ->with('details')->first();

                    if ($gateway_provider_record !== null) {
                        // @todo - get the credentials
                        $deets = [];
                        foreach ($gateway_provider_record->details as $detail) {
                            if ($detail->detail == 'access_credential') {
                                if ($detail->value == 'twilio_no') {
                                    $deets['twilio_no'] = $detail->misc['value'];
                                }

                                if ($detail->value == 'twilio_sid') {
                                    $deets['twilio_sid'] = $detail->misc['value'];
                                }

                                if ($detail->value == 'twilio_token') {
                                    $deets['twilio_token'] = $detail->misc['value'];
                                }
                            }
                        }
                        $gateway = new $gateway_provider_record->profile_class();
                    }
                }
        }


        return $results;
    }
}
