<?php

namespace App\Aggregates\Clients\Traits\Actions;

use App\StorableEvents\Clients\Activity\GatewayProviders\GatewayIntegrationCreated;

trait ClientGatewayActions
{
    public function createGatewayIntegration(string $provider_type, string $gateway_slug, string $nickname, string $created_by_user_id)
    {
        $this->recordThat(new GatewayIntegrationCreated($this->uuid(), $provider_type, $gateway_slug, $nickname, $created_by_user_id));

        return $this;
    }
}
