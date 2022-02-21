<?php

namespace App\Services\GatewayProviders\Profiles\SMS;

use App\Services\GatewayProviders\MessageInterpreters\MessageInterpreterService;
use App\Services\GatewayProviders\MessageInterpreters\SMS\StandardSMSInterpreter;
use App\Services\GatewayProviders\Profiles\GatewayProfile;

abstract class SMSGatewayProvider extends GatewayProfile implements SMSProfile
{
    protected array $credentials;
    protected MessageInterpreterService $interpreter;

    public function __construct(array $credentials, string $gateway_name, $user_id, string $interpreter = 'standard')
    {
        $this->credentials = $credentials;
        $this->interpreter = $this->initMessageInterpreter($interpreter, $user_id);
        parent::__construct($gateway_name);
    }

    private function initMessageInterpreter($interpreter, $user_id)
    {
        switch($interpreter)
        {
            default:
                // The standard SMS interpreter uses the User's aggregate to convert tokens.
                return new StandardSMSInterpreter($user_id);
        }
    }

    public function translateMessage(string $msg)
    {
        return $this->interpreter->translate($msg);
    }

    public function fireMsg($phone_number, $msg)
    {
        return true;
    }
}
