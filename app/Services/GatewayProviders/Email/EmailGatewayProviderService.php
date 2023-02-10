<?php

declare(strict_types=1);

namespace App\Services\GatewayProviders\Email;

use App\Domain\Clients\Models\ClientGatewayIntegration;
use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\Models\GatewayProviders\GatewayProvider;
use App\Models\GatewayProviders\GatewayProviderType;
use App\Services\GatewayProviders\GatewayProviderService;
use App\Services\GatewayProviders\Profiles\Email\EmailGatewayProvider;
use App\Services\GatewayProviders\Profiles\Email\Mailgun;

/**
 * @deprecated
 */
class EmailGatewayProviderService extends GatewayProviderService
{
    protected $provider_type_slug = 'email';
    protected EmailTemplate $email_template;
    protected EmailGatewayProvider $gateway;

    public function __construct(EmailTemplate $email_template)
    {
        $this->email_template = $email_template;
        $model                = GatewayProviderType::where('name', '=', $this->provider_type_slug)->first();
        parent::__construct($model);
        if ($this->email_template->client_id !== null) {
            $this->setAssociatedClient($this->email_template->client_id);
        }
    }

    public function initEmailGateway($user_id): void
    {
        if ($gateway = $this->getEmailGateway($user_id)) {
            $this->gateway = $gateway;
        }
    }

    public function getRawMessage(): string
    {
        return $this->email_template->markup;
    }

    public function getSubject(): string
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
        $msg     = $this->getRawMessage();
        $subject = $this->getSubject();
        return $this->gateway->fireMsg($email_address, $subject, $msg);
    }

    public function fireBulk()
    {
        return false;
    }

    private function getEmailGateway($user_id)
    {
        $results  = false;
        $model    = $this->email_template->gateway()->first();
        $provider = 'default_cnb'; //can't use default because that requires client_id

        if ($model !== null) {
            $provider = $model->value;
        }
        switch ($provider) {
            case 'default_cnb':
                /*
                $client_integration_record = ClientGatewayIntegration::whereClientId($this->client->id)
                    ->whereNickname($model->value)->whereActive(1)->whereGateway_slug('mailgun')->first();

                if($client_integration_record !== null)
                {
                    $gateway_provider_record = GatewayProvider::whereSlug($client_integration_record->gateway_slug)
                        ->with('details')->first();

                    if($gateway_provider_record !== null)
                    {
                        $deets = [];
                        foreach ($gateway_provider_record->details as $detail)
                        {
                            if($detail->detail == 'access_credential')
                            {
                                if($detail->value == 'mailgun_domain')
                                    $deets['mailgun_domain'] = $detail->misc['value'];

                                if($detail->value == 'mailgun_secret')
                                    $deets['mailgun_secret'] = $detail->misc['value'];

                                if($detail->value == 'mailgun_endpoint')
                                    $deets['mailgun_endpoint'] = $detail->misc['value'];

                                if($detail->value == 'mailgun_from_addr')
                                    $deets['mailgun_from_addr'] = $detail->misc['value'];
                            }
                        }
                    }
                }*/
                $deets   = [
                    'mailgun_domain' => env('MAILGUN_DOMAIN'),
                    'mailgun_secret' => env('MAILGUN_SECRET'),
                    'mailgun_endpoint' => env('MAILGUN_ENDPOINT'),
                    'mailgun_from_addr' => env('MAIL_FROM_ADDRESS'),
                ];
                $results = new Mailgun($deets, $user_id);

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
                        $deets = [];
                        foreach ($gateway_provider_record->details as $detail) {
                            if ($detail->detail == 'access_credential') {
                                if ($detail->value == 'mailgun_domain') {
                                    $deets['mailgun_domain'] = $detail->misc['value'];
                                }

                                if ($detail->value == 'mailgun_secret') {
                                    $deets['mailgun_secret'] = $detail->misc['value'];
                                }

                                if ($detail->value == 'mailgun_endpoint') {
                                    $deets['mailgun_endpoint'] = $detail->misc['value'];
                                }

                                if ($detail->value == 'mailgun_from_addr') {
                                    $deets['mailgun_from_addr'] = $detail->misc['value'];
                                }
                            }
                        }
                        //$gateway = new $gateway_provider_record->profile_class();
                    }
                }
        }


        return $results;
    }
}
