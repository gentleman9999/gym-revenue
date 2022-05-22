<?php

namespace App\Projectors\Clients;

use App\Models\GatewayProviders\ClientGatewayIntegration;
use App\Models\GatewayProviders\GatewayProvider;
use App\Models\GatewayProviders\GatewayProviderType;
use App\StorableEvents\Clients\Activity\GatewayProviders\GatewayIntegrationCreated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ClientGatewayProjector extends Projector
{
    public function onGatewayIntegrationCreated(GatewayIntegrationCreated $event)
    {
        $types = GatewayProviderType::getAllTypesAsArray();
        $gateway = GatewayProvider::whereSlug($event->slug)->whereActive(1)->first();
        ClientGatewayIntegration::firstOrCreate([
            'gateway_id' => $gateway->id,
            'gateway_slug' => $event->slug,
            'client_id' => $event->client,
            'provider_type' => $types[$event->type]['id'],
            'nickname' => $event->nickname,
            'active' => 1,
        ]);
    }
}
