<?php

namespace App\Projectors\Clients;

use App\Models\Clients\Features\ClientService;
use App\StorableEvents\Clients\Activity\Campaigns\ClientServiceEnabled;
use App\StorableEvents\Clients\ClientServices\ClientServiceAdded;
use App\StorableEvents\Clients\ClientServices\ClientServiceDisabled;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ClientServicesProjector extends Projector
{
    public function onClientServiceAdded(ClientServiceAdded $event)
    {
        ClientService::firstOrCreate(['client_id' => $event->client, 'slug' => $event->slug, 'feature_name' => $event->feature, 'active' => $event->active]);
    }

    public function onClientServiceEnabled(ClientServiceEnabled $event)
    {
        $clientService = ClientService::whereClientId($event->client)->whereSlug($event->slug)->findOrFail();
        $clientService->active = 1;
        $clientService->save();
    }

    public function onClientServiceDisabled(ClientServiceDisabled $event)
    {
        $clientService = ClientService::whereClientId($event->client)->whereSlug($event->slug)->findOrFail();
        $clientService->active = 0;
        $clientService->save();
    }
}
