<?php

namespace App\Domain\Clients;

use App\Domain\Clients\Events\ClientDeleted;
use App\Domain\Clients\Models\Client;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ClientProjector extends Projector
{
    public function onClientCreated(Events\ClientCreated $event): void
    {
        $client = new Client();
        $client->fill($event->payload);
        $client->id = $event->aggregateRootUuid();

        $default_team_name = $client->name . ' Home Office';
        preg_match_all('/(?<=\s|^)[a-z]/i', $default_team_name, $matches);
        $prefix = strtoupper(implode('', $matches[0]));
        $prefix = (strlen($prefix) > 3) ? substr($prefix, 0, 3) : $prefix;
        $client->prefix = $prefix;

        $client->save();
    }

    public function onClientUpdated(Events\ClientUpdated $event)
    {
        Client::withTrashed()->findOrFail($event->aggregateRootUuid())->updateOrFail($event->payload);
    }

    public function onClientTrashed(Events\ClientTrashed $event)
    {
        Client::withTrashed()->findOrFail($event->aggregateRootUuid())->delete();
    }

    public function onClientRestored(Events\ClientRestored $event)
    {
        Client::withTrashed()->findOrFail($event->aggregateRootUuid())->restore();
    }

    public function onClientDeleted(ClientDeleted $event): void
    {
        Client::withTrashed()->findOrFail($event->aggregateRootUuid())->forceDelete();
    }
}
