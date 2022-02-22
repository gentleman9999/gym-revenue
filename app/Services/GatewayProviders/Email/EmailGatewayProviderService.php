<?php

namespace App\Services\GatewayProfiles\Email;

use App\Models\Comms\EmailTemplates;
use App\Models\GatewayProviders\ClientGatewayIntegration;
use App\Models\GatewayProviders\GatewayProvider;
use App\Models\GatewayProviders\GatewayProviderType;
use App\Services\GatewayProviders\GatewayProviderService;
use App\Services\GatewayProviders\Profiles\Email\EmailGatewayProvider;
use App\Services\GatewayProviders\Profiles\Email\Mailgun;

class EmailGatewayProviderService extends GatewayProviderService
{
    protected $provider_type_slug = 'email';
    protected EmailTemplates $email_template;
    protected EmailGatewayProvider $gateway;
    public function __construct(EmailTemplates $email_template)
    {
        $this->email_template = $email_template;
        $model = GatewayProviderType::where('name', '=', $this->provider_type_slug)->first();
        parent::__construct($model);
        $this->setAssociatedClient($this->email_template->client_id);
    }

    public function initEmailGateway($user_id) : void
    {
        if($gateway = $this->getEmailGateway($user_id))
        {
            $this->gateway = $gateway;
        }
    }

    private function getEmailGateway($user_id)
    {
        $results = false;
        $model = $this->email_template->gateway()->first();

        if(!is_null($model))
        {
            switch($model->value)
            {
                case 'default_cnb':
                    $deets = [
                        'mailgun_domain' => env('MAILGUN_DOMAIN'),
                        'mailgun_secret' => env('MAILGUN_SECRET'),
                        'mailgun_endpoint' => env('MAILGUN_ENDPOINT'),
                        'mailgun_from_addr' => env('MAIL_FROM_ADDRESS')
                    ];
                    $results = new Mailgun($deets, $user_id);
                    break;
                // default will be the slug name given to the
                // client_gateway_integrations configuration

                default:
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
                                if($detail->detail == 'access_credentais')
                                {
                                    if($detail->value == 'mailgun_domain')
                                    {
                                        $value = json_decode($detail->misc);
                                        $deets['mailgun_domain'] = $value['value'];
                                    }
                                    if($detail->value == 'mailgun_secret')
                                    {
                                        $value = json_decode($detail->misc);
                                        $deets['mailgun_secret'] = $value['value'];
                                    }
                                    if($detail->value == 'mailgun_endpoint')
                                    {
                                        $value = json_decode($detail->misc);
                                        $deets['mailgun_endpoint'] = $value['value'];
                                    }
                                    if($detail->value == 'mailgun_from_addr')
                                    {
                                        $value = json_decode($detail->misc);
                                        $deets['mailgun_from_addr'] = $value['value'];
                                    }
                                }
                            }
                            $gateway = new $gateway_provider_record->profile_class();
                        }
                    }

            }
        }

        return $results;
    }

    public function getRawMessage() : string
    {
        return $this->email_template->markup;
    }

    public function getSubject() : string
    {
        return $this->email_template->subject ?? '';
    }

    public function getTranslatedMessage()
    {
        $raw_message = $this->getRawMessage();

        return $this->gateway->translateMessage($raw_message);
    }

    public function fire($email_address)
    {
        $results = false;

        $msg = $this->getRawMessage();
        $subject = $this->getSubject();
        $response = $this->gateway->fireMsg($email_address, $subject, $msg);

        return $results;
    }

    public function fireBulk()
    {
        $results = false;

        return $results;
    }


}
