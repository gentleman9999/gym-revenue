<?php

declare(strict_types=1);

namespace App\Services\GatewayProviders\Profiles\SMS;

use App\Services\GatewayProviders\MessageInterpreters\MessageInterpreterService;
use App\Services\GatewayProviders\MessageInterpreters\SMS\StandardSMSInterpreter;
use App\Services\GatewayProviders\Profiles\GatewayProfile;

abstract class SMSGatewayProvider extends GatewayProfile implements SMSProfile
{
    protected array $credentials;
    protected MessageInterpreterService $interpreter;

    public function __construct(
        array $credentials,
        string $gateway_name,
        string $user_id,
        string $interpreter = 'standard'
    ) {
        $this->credentials = $credentials;
        $this->interpreter = $this->initMessageInterpreter($interpreter, $user_id);
        parent::__construct($gateway_name);
    }

    public function translateMessage(string $msg): string
    {
        return $this->interpreter->translate($msg);
    }

    /**
     * @param mixed  $_phone_number
     * @param string $_msg
     *
     * @return mixed
     */
    public function fireMsg($_phone_number, string $_msg)
    {
        return true;
    }

    private function initMessageInterpreter(string $_interpreter, string $user_id): StandardSMSInterpreter
    {
        return new StandardSMSInterpreter($user_id);
    }
}
