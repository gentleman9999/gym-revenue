<?php

namespace App\Services\GatewayProviders\Profiles\Voice;

use App\Services\GatewayProviders\Profiles\GatewayProfile;

abstract class VoiceGatewayProvider extends GatewayProfile implements VoiceProfile
{
    public function __construct(protected array $credentials, protected string $gateway_name)
    {
        $this->credentials = $credentials;
        parent::__construct($gateway_name);
    }
}
