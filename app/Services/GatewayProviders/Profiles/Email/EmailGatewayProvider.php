<?php

namespace App\Services\GatewayProviders\Profiles\Email;

use App\Services\GatewayProviders\Profiles\GatewayProfile;

abstract class EmailGatewayProvider extends GatewayProfile
{
    public function __construct()
    {
        parent::__construct();
    }
}
