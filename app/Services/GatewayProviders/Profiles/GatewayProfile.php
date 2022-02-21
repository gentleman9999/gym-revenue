<?php

namespace App\Services\GatewayProviders\Profiles;

abstract class GatewayProfile
{
    protected string $gateway_name;

    public function __construct(string $gateway_name)
    {
        $this->gateway_name = $gateway_name;
    }

    public function getGatewayName()
    {
        return $this->gateway_name;
    }
}
