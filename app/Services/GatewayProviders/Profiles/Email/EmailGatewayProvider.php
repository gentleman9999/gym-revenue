<?php

declare(strict_types=1);

namespace App\Services\GatewayProviders\Profiles\Email;

use App\Services\GatewayProviders\MessageInterpreters\Email\StandardEmailInterpreter;
use App\Services\GatewayProviders\MessageInterpreters\MessageInterpreterService;
use App\Services\GatewayProviders\Profiles\GatewayProfile;

abstract class EmailGatewayProvider extends GatewayProfile implements EmailProfile
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
     * @param mixed $phone_number
     * @param string $subject
     * @param string $msg
     *
     * @return mixed
     */
    public function fireMsg($phone_number, string $subject, string $msg)
    {
        return true;
    }

    private function initMessageInterpreter(string $_interpreter, string $user_id): StandardEmailInterpreter
    {
        // The standard SMS interpreter uses the User's aggregate to convert tokens.
        return new StandardEmailInterpreter($user_id);
    }
}
