<?php

declare(strict_types=1);

namespace App\Services\GatewayProviders\Profiles\Voice;

use App\Services\GatewayProviders\Profiles\GatewayProfile;

abstract class VoiceGatewayProvider extends GatewayProfile implements VoiceProfile
{
    /**
     * @param array<string, mixed>  $credentials
     * @param string $gateway_name
     */
    public function __construct(protected array $credentials, protected string $gateway_name)
    {
        parent::__construct($gateway_name);
    }
}
