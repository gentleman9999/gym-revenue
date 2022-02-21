<?php

namespace App\Services\GatewayProfiles\SMS;

use App\Models\Comms\SmsTemplates;
use App\Models\GatewayProviders\ClientGatewayIntegration;
use App\Models\GatewayProviders\GatewayProvider;
use App\Models\GatewayProviders\GatewayProviderType;
use App\Services\GatewayProviders\GatewayProviderService;
use App\Services\GatewayProviders\Profiles\SMS\SMSGatewayProvider;
use App\Services\GatewayProviders\Profiles\SMS\Twilio;

class SMSGatewayProviderService extends GatewayProviderService
{
    protected $provider_type_slug = 'sms';
    protected SmsTemplates $sms_template;
    protected SMSGatewayProvider $gateway;
    public function __construct(SmsTemplates $sms_template)
    {
        $this->sms_template = $sms_template;
        $model = GatewayProviderType::where('name', '=', $this->provider_type_slug)->first();
        parent::__construct($model);
        $this->setAssociatedClient($this->sms_template->client_id);
    }

    public function initSMSGateway($user_id) : void
    {
        if($gateway = $this->getSMSGateway($user_id))
        {
            $this->gateway = $gateway;
        }
    }

    private function getSMSGateway($user_id)
    {
        $results = false;
        $model = $this->sms_template->gateway()->first();

        if(!is_null($model))
        {
            switch($model->value)
            {
                case 'default_cnb':
                    $deets = [
                        'mailgun_' => env('TWILIO_NO'),
                        'MAILGUN_ENDPOINT' => env('TWILIO_SID'),
                        'MAILGUN_ENDPOINT' => env('TWILIO_TOKEN')
                    ];
                    $results = new Twilio($deets, $user_id);
                break;
                // default will be the slug name given to the
                // client_gateway_integrations configuration
                default:
                    /*
                     $client_integration_record = ClientGatewayIntegration::whereClientId($this->client->id)
                        ->whereNickname($model->value)->whereActive(1)->first();

                    if(!is_null($client_integration_record))
                    {
                        $gateway_provider_record = GatewayProvider::whereSlug($client_integration_record->gateway_slug)
                            ->with('details')->first();

                        if(!is_null($gateway_provider_record))
                        {
                            // @todo - get the credentials
                            $deets = [];
                            foreach ($gateway_provider_record->details as $detail)
                            {
                                if($detail->detail == 'access_credentaisl')
                            }
                            $gateway = new $gateway_provider_record->profile_class();
                        }
                    }
                     */

            }
        }

        return $results;
    }

    public function getRawMessage() : string
    {
        return $this->sms_template->markup;
    }

    public function getTranslatedMessage()
    {
        $results = false;
        $raw_message = $this->getRawMessage();

        return $this->gateway->translateMessage($raw_message);
    }

    public function fire($phone_number)
    {
        $results = false;

        $msg = $this->getRawMessage();
        $response = $this->gateway->fireMsg($phone_number, $msg);

        return $results;
    }

    public function fireBulk()
    {
        $results = false;

        return $results;
    }


}
