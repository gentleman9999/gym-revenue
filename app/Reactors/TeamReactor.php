<?php

namespace App\Reactors;

use App\Aggregates\Clients\ClientAggregate;
use App\Domain\Teams\Events\TeamCreated;
use App\Domain\Teams\Models\Team;
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
