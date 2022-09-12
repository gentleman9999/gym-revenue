<?php

declare(strict_types=1);

namespace App\Services\GatewayProviders;

use App\Domain\Clients\Projections\Client;
use App\Models\GatewayProviders\GatewayProviderType;
use App\Services\GatewayProviders\Exceptions\GatewayProviderException;

abstract class GatewayProviderService
{
    protected GatewayProviderType  $provider_type;
    protected ?Client              $client = null;

    public function __construct(GatewayProviderType $provider_type)
    {
        $this->provider_type = $provider_type;
    }

    /**
     * @param string|null $client_id
     *
     * @return $this
     * @throws GatewayProviderException
     */
    public function setAssociatedClient(?string $client_id): static
    {
        if ($client_id !== null) {
            $this->client = Client::find($client_id) ?: throw new GatewayProviderException('Invalid client id');
        }

        return $this;
    }
}
