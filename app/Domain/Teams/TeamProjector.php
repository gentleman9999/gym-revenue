<?php

namespace App\Domain\Teams;

use App\Domain\Teams\Events\TeamCreated;
use App\Domain\Teams\Events\TeamDeleted;
use App\Domain\Teams\Events\TeamUpdated;
use App\Domain\Teams\Models\Team;
use App\Models\TeamDetail;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class TeamProjector extends Projector
{
    public function onTeamCreated(TeamCreated $event)
    {
        $team = new Team();
        $team->fill($event->payload);
        $team->id = $event->aggregateRootUuid();
        $team->client_id = $event->payload['client_id'] ?? null;
        $team->save();
        //TODO:just use a team_location pivot table
        foreach ($event->payload['locations'] ?? [] as $location_gymrevenue_id) {
            TeamDetail::create(['team_id' => $event->aggregateRootUuid(), 'name' => 'team-location', 'value' => $location_gymrevenue_id]);
        }
    }

    public function onTeamDeleted(TeamDeleted $event)
    {
        Team::findOrFail($event->aggregateRootUuid())->deleteOrFail();
    }

    public function onTeamUpdated(TeamUpdated $event)
    {
        Team::findOrFail($event->aggregateRootUuid())->updateOrFail($event->payload);
        TeamDetail::whereTeamId($event->aggregateRootUuid())->whereName('team-location')->delete();
        foreach ($event->payload['locations'] as $location_gymrevenue_id) {
            TeamDetail::create(['team_id' => $event->aggregateRootUuid(), 'name' => 'team-location', 'value' => $location_gymrevenue_id]);
        }
    }
}
