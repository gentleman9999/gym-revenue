<?php

namespace App\Services\GatewayProviders\Profiles\Email;

use Mailgun\Mailgun as MailgunClient;

class Mailgun extends EmailGatewayProvider
{
    protected $mailgun_domain;
    protected $mailgun_secret;
    protected $mailgun_endpoint;
    protected $mailgun_from_addr;
    protected MailgunClient $client;

    public function __construct(array $access_credentials, $user_id, $interpreter = 'standard')
    {
        $details = [
            'mailgun_domain' => $this->mailgun_domain = $access_credentials['mailgun_domain'] ?? null,
            'mailgun_secret' => $this->mailgun_secret = $access_credentials['mailgun_secret'] ?? null,
            'mailgun_endpoint' => $this->mailgun_endpoint = $access_credentials['mailgun_endpoint'] ?? null,
            'mailgun_from_addr' => $this->mailgun_from_addr = $access_credentials['mailgun_from_addr'] ?? null,
        ];
        parent::__construct($details, 'Mailgun', $user_id, $interpreter);
        //$this->client = new MailgunClient($this->mailgun_secret);
    }

    public function fireMsg($email_address, $subject, $msg)
    {
        $results = false;

        $clean_msg = $this->translateMessage($msg);

        $Mailgun = MailgunClient::create($this->mailgun_secret);

        $Mailgun->messages()->send($this->mailgun_domain, [
                'from' => env('MAIL_FROM_ADDRESS'),
                'to' => $email_address,
                'subject' => $subject,
                'html' => $clean_msg,
                //'recipient-variables' => $this->mailgun_domain
        ]);

        if ($Mailgun) {
            $results = $Mailgun->getLastResponse();
        }

        return $results;
    }

    public function fireBulkMsg()
    {
        // TODO: Implement fireBulkMsg() method.
    }
}
