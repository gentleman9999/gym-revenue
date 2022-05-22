<?php

namespace App\Services\GatewayProviders\MessageInterpreters;

use App\Aggregates\Users\UserAggregate;

abstract class MessageInterpreterService
{
    protected UserAggregate $user_aggy;

    public function __construct($user_id)
    {
        $this->user_aggy = UserAggregate::retrieve($user_id);
    }

    public function getTranslatedValue(string $token_key)
    {
        return $this->user_aggy->getProperty($token_key);
    }
}
