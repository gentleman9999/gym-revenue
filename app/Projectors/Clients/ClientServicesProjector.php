<?php

namespace App\Projectors\Clients;

use App\Models\Clients\ClientDetail;
use App\Models\Clients\Features\ClientService;
use App\StorableEvents\Clients\Activity\Campaigns\ClientServiceEnabled;
use App\StorableEvents\Clients\ClientServices\ClientServiceAdded;
use App\StorableEvents\Clients\ClientServices\ClientServiceDisabled;
use App\StorableEvents\Clients\ClientServices\ClientServicesSet;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ClientServicesProjector extends Projector
{
    public function onClientServiceAdded(ClientServiceAdded $event)
    {
        ClientService::firstOrCreate(['client_id' => $event->client, 'slug' => $event->slug, 'feature_name' => $event->feature, 'active' => $event->active]);
    }

    public function onClientServiceEnabled(ClientServiceEnabled $event)
    {
        ClientDetail::firstOrCreate(['clientId' => $event->clientId, 'detail' => 'service_slug', 'value' => $event->slug]);
    }

    public function onClientServiceDisabled(ClientServiceDisabled $event)
    {
        ClientDetail::whereClientId($event->clientId)->whereDetail('service_slug')->whereValue($event->slug)->deleteOrFail();
    }

    public function onClientServicesSet(ClientServicesSet $event)
    {
        ClientDetail::whereClientId($event->client)->whereDetail('service_slug')->delete();
//        dd($event->services);
        foreach ($event->services as $service_slug) {
            ClientDetail::create(['client_id' => $event->client, 'detail' => 'service_slug', 'value' => $service_slug]);
        }
    }
}
