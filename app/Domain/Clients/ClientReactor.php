<?php

namespace App\Domain\Clients;

use App\Domain\Clients\Events\ClientCreated;
use App\Domain\Clients\Models\Client;
use App\Domain\Teams\Actions\CreateTeam;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class ClientReactor extends Reactor
{
    public function onClientCreated(ClientCreated $event): void
    {
        $client = Client::findOrFail($event->aggregateRootUuid())->writeable();

        $default_team_name = $client->name . ' Home Office';

        $default_team = CreateTeam::run([
            'client_id' => $client->id,
            'name' => $default_team_name,
            'home_team' => true,
        ]);
        $client->home_team_id = $default_team->id;
        $client->save();
    }
}
