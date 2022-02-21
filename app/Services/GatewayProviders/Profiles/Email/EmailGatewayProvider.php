<?php

namespace App\Services\GatewayProviders\Profiles\Email;

use App\Services\GatewayProviders\MessageInterpreters\MessageInterpreterService;
use App\Services\GatewayProviders\MessageInterpreters\Email\StandardEmailInterpreter;
use App\Services\GatewayProviders\Profiles\GatewayProfile;

abstract class EmailGatewayProvider extends GatewayProfile implements EmailProfile
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
                return new StandardEmailInterpreter($user_id);
        }
    }

    public function translateMessage(string $msg)
    {
        return $this->interpreter->translate($msg);
    }

    public function fireMsg($phone_number, $subject, $msg)
    {
        return true;
    }
}
