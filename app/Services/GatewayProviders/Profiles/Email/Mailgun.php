<?php

declare(strict_types=1);

namespace App\Services\GatewayProviders\Profiles\Email;

use Mailgun\Mailgun as MailgunClient;
use Psr\Http\Message\ResponseInterface;

class Mailgun extends EmailGatewayProvider
{
    protected ?string $mailgun_domain;
    protected ?string $mailgun_secret;
    protected ?string $mailgun_endpoint;
    protected ?string $mailgun_from_addr;
    protected MailgunClient $client;

    public function __construct(array $access_credentials, string $user_id, string $interpreter = 'standard')
    {
        $details = [
            'mailgun_domain' => $this->mailgun_domain       = $access_credentials['mailgun_domain'] ?? null,
            'mailgun_secret' => $this->mailgun_secret       = $access_credentials['mailgun_secret'] ?? null,
            'mailgun_endpoint' => $this->mailgun_endpoint   = $access_credentials['mailgun_endpoint'] ?? null,
            'mailgun_from_addr' => $this->mailgun_from_addr = $access_credentials['mailgun_from_addr'] ?? null,
        ];
        parent::__construct($details, 'Mailgun', $user_id, $interpreter);
        //$this->client = new MailgunClient($this->mailgun_secret);
    }

    /**
     * @param mixed $email_address
     * @param string $subject
     * @param string $msg
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function fireMsg($email_address, string $subject, string $msg): ?ResponseInterface
    {
        $clean_msg = $this->translateMessage($msg);
        $mailgun   = MailgunClient::create($this->mailgun_secret);
        $mailgun->messages()->send($this->mailgun_domain, [
            'from' => env('MAIL_FROM_ADDRESS'),
            'to' => $email_address,
            'subject' => $subject,
            'html' => $clean_msg,
                //'recipient-variables' => $this->mailgun_domain
        ]);

        return $mailgun->getLastResponse();
    }

    public function fireBulkMsg(): void
    {
        // TODO: Implement fireBulkMsg() method.
    }
}
