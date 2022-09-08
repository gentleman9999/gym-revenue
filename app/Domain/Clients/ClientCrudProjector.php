<?php

namespace App\Domain\Clients;

use App\Domain\Clients\Events\ClientCreated;
use App\Domain\Clients\Events\ClientDeleted;
use App\Domain\Clients\Events\ClientRestored;
use App\Domain\Clients\Events\ClientTrashed;
use App\Domain\Clients\Events\ClientUpdated;
use App\Domain\Clients\Models\ClientGatewaySetting;
use App\Domain\Clients\Models\ClientSocialMedia;
use App\Domain\Clients\Projections\Client;
use App\Domain\Clients\Projections\ClientActivity;
use App\Domain\Clients\Projections\ClientDetail;
use App\Models\ClientCommunicationPreference;
use App\Models\ClientEmailLog;
use App\Models\Clients\ClientBillableActivity;
use App\Models\Clients\Features\ClientService;
use App\Models\ClientSmsLog;
use App\Models\GatewayProviders\ClientGatewayIntegration;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ClientCrudProjector extends Projector
{
    public function onStartingEventReplay()
    {
        Client::truncate();
        ClientActivity::truncate();
        ClientBillableActivity::truncate();
        ClientDetail::truncate();
        ClientEmailLog::truncate();
        ClientGatewayIntegration::truncate();
        ClientGatewaySetting::truncate();
        ClientService::truncate();
        ClientSmsLog::truncate();
        ClientSocialMedia::truncate();
    }

    public function onClientCreated(ClientCreated $event): void
    {
        $client = (new Client())->writeable();
        $client->fill($event->payload);
        $client->id = $event->aggregateRootUuid();

        $default_team_name = $client->name . ' Home Office';
        preg_match_all('/(?<=\s|^)[a-z]/i', $default_team_name, $matches);
        $prefix = strtoupper(implode('', $matches[0]));
        $prefix = (strlen($prefix) > 3) ? substr($prefix, 0, 3) : $prefix;
        $client->prefix = $prefix;
        $client->services = $event->payload['services'];

        $client->save();

        (new ClientCommunicationPreference())->writeable()->forceFill(['client_id' => $client->id, 'email' => true, 'sms' => false])->save();
    }

    public function onClientUpdated(ClientUpdated $event): void
    {
        Client::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->updateOrFail($event->payload);
    }

    public function onClientTrashed(ClientTrashed $event): void
    {
        Client::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
    }

    public function onClientRestored(ClientRestored $event): void
    {
        Client::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onClientDeleted(ClientDeleted $event): void
    {
        Client::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }
}
