<?php

namespace App\Services\GatewayProviders;

use App\Domain\Clients\Projections\Client;
use App\Models\GatewayProviders\GatewayProviderType;

abstract class GatewayProviderService
{
    protected GatewayProviderType $provider_type;
    protected Client $client;

    public function __construct(GatewayProviderType $provider_type)
    {
        $this->provider_type = $provider_type;
    }

    public function setAssociatedClient(string $client_id)
    {
        $model = Client::find($client_id);

        if (! is_null($model)) {
            $this->client = $model;
        } else {
            // @todo - throw a custom exception cuz those are cool
        }

        return $this;
    }
}
