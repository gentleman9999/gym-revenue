<?php

namespace App\Services\GatewayProviders\Profiles\SMS;

use Twilio\Rest\Client as TwilioClient;

class TwilioSMS extends SMSGatewayProvider
{
    protected $twilio_no;
    protected $twilio_sid;
    protected $twilio_token;
    protected TwilioClient $client;

    public function __construct(array $access_credentials, $user_id, $interpreter = 'standard')
    {
        $deets = [
            'twilio_no' => $this->twilio_no = $access_credentials['twilio_no'] ?? null,
            'twilio_sid' => $this->twilio_sid = $access_credentials['twilio_sid'] ?? null,
            'twilio_token' => $this->twilio_token = $access_credentials['twilio_token'] ?? null,
        ];
        parent::__construct($deets, 'TwilioSMS', $user_id, $interpreter);
        $this->client = new TwilioClient($this->twilio_sid, $this->twilio_token);
    }

    public function fireMsg($phone_number, $msg)
    {
        $results = false;

        $clean_msg = $this->translateMessage($msg);
        $payload = ['from' => $this->twilio_no, 'body' => $clean_msg];
        $message = $this->client->messages->create($phone_number, $payload);

        if ($message) {
            $results = $message->sid;
        }

        return $results;
    }

    public function fireBulkMsg()
    {
    }
}
