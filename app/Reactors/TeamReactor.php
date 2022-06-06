<?php

namespace App\Reactors;

use App\Aggregates\Clients\ClientAggregate;
use App\Models\Team;
use App\StorableEvents\Teams\TeamCreated;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class TeamReactor extends Reactor
{
    public function onTeamCreated(TeamCreated $event)
    {
        $team = Team::findOrFail($event->aggregateRootUuid());

        if ($team->client_id) {
            //TODO:make actions
            ClientAggregate::retrieve($team->client_id)->attachTeamToClient($team->id)->persist();
        }
    }
}
