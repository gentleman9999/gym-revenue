<?php

declare(strict_types=1);

namespace App\Services\GatewayProviders\Profiles;

use App\Models\GatewayProviders\GatewayProvider;

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

    public function getGatewayProvider(): ?GatewayProvider
    {
        return GatewayProvider::where(['name' => $this->gateway_name])->first();
    }
}
