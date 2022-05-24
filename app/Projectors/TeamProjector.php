<?php

namespace App\Projectors;

use App\Models\Team;
use App\Models\TeamDetail;
use App\StorableEvents\Teams\TeamCreated;
use App\StorableEvents\Teams\TeamDeleted;
use App\StorableEvents\Teams\TeamUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class TeamProjector extends Projector
{
    public function onTeamCreated(TeamCreated $event)
    {
        //get only the keys we care about (the ones marked as fillable)
        $team_table_data = array_filter($event->payload, function ($key) {
            return in_array($key, (new Team())->getFillable());
        }, ARRAY_FILTER_USE_KEY);
        //TODO: lookup an account owner instead of using Angel's hardcoded id
        if (! array_key_exists('user_id', $team_table_data)) {
            $team_table_data['user_id'] = 1;
        }
        $team = Team::create($team_table_data);
    }

    public function onTeamDeleted(TeamDeleted $event)
    {
        Team::findOrFail($event->id)->deleteOrFail();
    }

    public function onTeamUpdated(TeamUpdated $event)
    {
        $team_table_data = array_filter($event->payload, function ($key) {
            return in_array($key, (new Team())->getFillable());
        }, ARRAY_FILTER_USE_KEY);
        Team::findOrFail($event->aggregateRootUuid())->update($team_table_data);
        TeamDetail::whereTeamId($event->aggregateRootUuid())->whereName('team-location')->delete();
        foreach ($event->payload['locations'] as $location_gymrevenue_id) {
            TeamDetail::create(['team_id' => $event->aggregateRootUuid(), 'name' => 'team-location', 'value' => $location_gymrevenue_id]);
        }
    }
}
