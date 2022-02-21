<?php

namespace App\Services\GatewayProfiles\Email;

use App\Models\Comms\EmailTemplates;
use App\Models\GatewayProviders\GatewayProviderType;
use App\Services\GatewayProviders\GatewayProviderService;
use App\Services\GatewayProviders\Profiles\Email\EmailGatewayProvider;

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
        $this->setAssociatedClient($this->sms_template->client_id);
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

                    $results = '';// new Twilio($deets, $user_id);
                    break;
                // default will be the slug name given to the
                // client_gateway_integrations configuration
                default:


            }
        }

        return $results;
    }

    public function getRawMessage() : string
    {
        return $this->email_template->markup;
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
        $response = $this->gateway->fireMsg($email_address, $msg);

        return $results;
    }

    public function fireBulk()
    {
        $results = false;

        return $results;
    }


}
